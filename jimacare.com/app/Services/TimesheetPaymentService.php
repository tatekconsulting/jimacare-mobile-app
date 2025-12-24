<?php

namespace App\Services;

use App\Models\Timesheet;
use App\Models\TimesheetPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TimesheetPaymentService
{
    protected $stripeService;

    public function __construct(StripePaymentLinkService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Generate payment for a carer based on approved timesheets
     * 
     * @param int $clientId
     * @param int $carerId
     * @param string $periodType 'weekly' or 'monthly'
     * @param Carbon|null $periodStart
     * @param Carbon|null $periodEnd
     * @return TimesheetPayment|null
     */
    public function generatePayment(
        int $clientId,
        int $carerId,
        string $periodType = 'weekly',
        ?Carbon $periodStart = null,
        ?Carbon $periodEnd = null
    ): ?TimesheetPayment {
        // Determine period dates
        if (!$periodStart) {
            if ($periodType === 'weekly') {
                $periodStart = Carbon::now()->startOfWeek();
                $periodEnd = Carbon::now()->endOfWeek();
            } else {
                $periodStart = Carbon::now()->startOfMonth();
                $periodEnd = Carbon::now()->endOfMonth();
            }
        }

        if (!$periodEnd) {
            $periodEnd = $periodStart->copy()->add($periodType === 'weekly' ? 6 : 30, 'days');
        }

        // Get approved timesheets for this period
        $timesheets = Timesheet::where('client_id', $clientId)
            ->where('carer_id', $carerId)
            ->where('status', 'approved')
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->whereNull('paid_at') // Not already paid
            ->get();

        if ($timesheets->isEmpty()) {
            return null;
        }

        // Calculate totals
        $totalHours = $timesheets->sum('hours_worked');
        
        // Get contract to determine correct pricing
        $contractId = $timesheets->first()->contract_id;
        $contract = \App\Models\Contract::find($contractId);
        
        if ($contract && $contract->hourly_rate) {
            // Use contract's pricing system (66.6% provider, 33.3333% platform)
            $clientRate = $contract->hourly_rate;
            $providerRate = $contract->getProviderHourlyRate();
            $platformFeePerHour = $contract->getPlatformFeeHourly();
            
            // Calculate based on contract rates
            $subtotal = $providerRate * $totalHours; // What provider earns (66.6%)
            $platformFee = $platformFeePerHour * $totalHours; // Platform fee (33.3333%)
            $totalAmount = $clientRate * $totalHours; // What client pays
            $hourlyRate = $providerRate; // Store provider rate in payment record
        } else {
            // Fallback: Calculate from timesheet hourly_rate (assumed to be client rate)
            $clientRatePerHour = $timesheets->first()->hourly_rate ?? 0;
            $totalAmount = $timesheets->sum('total_amount'); // What client pays
            
            // Calculate provider earnings (66.6% of client rate)
            $providerRatePerHour = ($clientRatePerHour * 66.6667) / 100;
            $subtotal = $providerRatePerHour * $totalHours; // What provider earns
            
            // Calculate platform fee (33.3333% of client rate)
            $platformFee = ($clientRatePerHour * 33.3333) / 100 * $totalHours;
            
            // Apply minimum rate enforcement if contract exists
            if ($contract) {
                $minimumRate = $contract->getMinimumProviderRate();
                if ($providerRatePerHour < $minimumRate) {
                    $providerRatePerHour = $minimumRate;
                    $subtotal = $minimumRate * $totalHours;
                    $platformFee = $totalAmount - $subtotal; // Adjust platform fee
                }
            }
            
            $hourlyRate = $providerRatePerHour; // Store provider rate
        }

        // Get contract ID (use first timesheet's contract)
        $contractId = $timesheets->first()->contract_id;

        // Create payment record
        $payment = TimesheetPayment::create([
            'client_id' => $clientId,
            'carer_id' => $carerId,
            'contract_id' => $contractId,
            'period_type' => $periodType,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'total_hours' => $totalHours,
            'hourly_rate' => $hourlyRate,
            'subtotal' => $subtotal,
            'platform_fee' => $platformFee,
            'total_amount' => $totalAmount,
            'timesheet_ids' => $timesheets->pluck('id')->toArray(),
            'status' => 'pending',
        ]);

        return $payment;
    }

    /**
     * Generate payments for all carers for a client (weekly or monthly)
     */
    public function generatePaymentsForClient(int $clientId, string $periodType = 'weekly'): array
    {
        // Get job IDs posted by this client
        $jobIds = \App\Models\Contract::where('user_id', $clientId)->pluck('id');
        
        // Get accepted application IDs for these jobs
        $acceptedApplicationIds = \App\Models\JobApplication::whereIn('contract_id', $jobIds)
            ->where('status', 'accepted')
            ->pluck('id');
        
        // Get contracts filled by these applications
        $filledContractIds = \App\Models\Contract::whereIn('filled_by_application_id', $acceptedApplicationIds)
            ->pluck('id');
        
        // Also get contracts where the application is accepted
        $acceptedContractIds = \App\Models\JobApplication::whereIn('contract_id', $jobIds)
            ->where('status', 'accepted')
            ->pluck('contract_id')
            ->unique();
        
        // Combine all contract IDs where service providers have been accepted
        $validContractIds = $filledContractIds->merge($acceptedContractIds)->unique();
        
        // Get all unique carers with approved timesheets, but only those who have been accepted
        $acceptedCarerIds = \App\Models\JobApplication::whereIn('contract_id', $validContractIds)
            ->where('status', 'accepted')
            ->pluck('carer_id')
            ->unique();
        
        // Get carers with approved timesheets who are also accepted
        $carers = Timesheet::where('client_id', $clientId)
            ->whereIn('carer_id', $acceptedCarerIds)
            ->where('status', 'approved')
            ->whereNull('paid_at')
            ->distinct()
            ->pluck('carer_id')
            ->unique();

        $payments = [];
        foreach ($carers as $carerId) {
            $payment = $this->generatePayment($clientId, $carerId, $periodType);
            if ($payment) {
                $payments[] = $payment;
            }
        }

        return $payments;
    }

    /**
     * Mark timesheets as paid after successful payment
     */
    public function markTimesheetsAsPaid(TimesheetPayment $payment): void
    {
        Timesheet::whereIn('id', $payment->timesheet_ids ?? [])
            ->update([
                'paid_at' => now(),
                'status' => 'paid',
            ]);

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }
}

