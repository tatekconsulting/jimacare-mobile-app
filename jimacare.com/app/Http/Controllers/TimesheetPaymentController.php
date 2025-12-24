<?php

namespace App\Http\Controllers;

use App\Models\TimesheetPayment;
use App\Services\TimesheetPaymentService;
use App\Services\StripePaymentLinkService;
use App\Models\UserNotification;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class TimesheetPaymentController extends Controller
{
    protected $paymentService;
    protected $stripeService;

    public function __construct(
        TimesheetPaymentService $paymentService,
        StripePaymentLinkService $stripeService
    ) {
        $this->paymentService = $paymentService;
        $this->stripeService = $stripeService;
        $this->middleware('auth');
    }

    /**
     * Generate and send payment links for a client (weekly/monthly)
     * NOTE: Only admins can generate payment links. Clients receive them automatically.
     */
    public function generateAndSendPayments(Request $request)
    {
        $user = auth()->user();
        $userRole = $user->role->slug ?? '';
        
        // Only admins can generate payment links
        if ($userRole !== 'admin' && $user->role_id !== 1) {
            abort(403, 'Unauthorized. Only administrators can generate payment links.');
        }

        $request->validate([
            'period_type' => 'required|in:weekly,monthly',
            'client_id' => 'required|exists:users,id', // Admin must specify client
            'carer_id' => 'nullable|exists:users,id',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after:period_start',
        ]);

        $clientId = $request->client_id; // Admin must specify client
        $periodType = $request->period_type;
        $periodStart = $request->period_start ? Carbon::parse($request->period_start) : null;
        $periodEnd = $request->period_end ? Carbon::parse($request->period_end) : null;

        // Generate payments
        if ($request->carer_id) {
            // Generate for specific carer
            $payment = $this->paymentService->generatePayment(
                $clientId,
                $request->carer_id,
                $periodType,
                $periodStart,
                $periodEnd
            );

            if (!$payment) {
                return back()->with('error', 'No approved timesheets found for this period.');
            }

            // Create Stripe payment link
            $result = $this->stripeService->createPaymentLink($payment);

            if ($result['success']) {
                // Send notification to client
                $this->sendPaymentLinkToClient($payment);
                
                return back()->with('success', 'Payment link generated and sent to client.');
            } else {
                return back()->with('error', 'Failed to create payment link: ' . $result['error']);
            }
        } else {
            // Generate for all carers
            $payments = $this->paymentService->generatePaymentsForClient($clientId, $periodType);

            if (empty($payments)) {
                return back()->with('error', 'No approved timesheets found for this period.');
            }

            $successCount = 0;
            foreach ($payments as $payment) {
                $result = $this->stripeService->createPaymentLink($payment);
                if ($result['success']) {
                    $this->sendPaymentLinkToClient($payment);
                    $successCount++;
                }
            }

            return back()->with('success', "Generated and sent {$successCount} payment link(s).");
        }
    }

    /**
     * Send payment link to client via notification
     */
    protected function sendPaymentLinkToClient(TimesheetPayment $payment): void
    {
        $carerName = $payment->carer->firstname . ' ' . substr($payment->carer->lastname ?? '', 0, 1) . '.';
        $period = $payment->period_start->format('M d') . ' - ' . $payment->period_end->format('M d, Y');

        UserNotification::create([
            'user_id' => $payment->client_id,
            'type' => 'payment_link',
            'title' => 'Payment Link Generated',
            'message' => "Payment link for {$carerName} ({$period}): {$payment->total_hours}h @ £{$payment->hourly_rate}/hr = £{$payment->total_amount}",
            'action_url' => $payment->stripe_payment_link_url,
            'data' => json_encode([
                'payment_id' => $payment->id,
                'carer_name' => $carerName,
                'total_amount' => $payment->total_amount,
                'payment_link_url' => $payment->stripe_payment_link_url,
            ]),
        ]);

        // TODO: Send email notification as well
        // Mail::to($payment->client->email)->send(new PaymentLinkMail($payment));
    }

    /**
     * View payment history for client
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $userRole = $user->role->slug ?? '';
        
        $query = TimesheetPayment::with(['carer', 'client', 'contract']);

        if ($userRole === 'client') {
            // Clients see payments only for service providers who have worked for them
            // (i.e., have accepted applications and timesheets)
            $query->where('client_id', $user->id);
            
            // Get job IDs posted by this client
            $jobIds = \App\Models\Contract::where('user_id', $user->id)->pluck('id');
            
            // Get accepted application IDs for these jobs
            $acceptedApplicationIds = JobApplication::whereIn('contract_id', $jobIds)
                ->where('status', 'accepted')
                ->pluck('id');
            
            // Get contracts filled by these applications
            $filledContractIds = \App\Models\Contract::whereIn('filled_by_application_id', $acceptedApplicationIds)
                ->pluck('id');
            
            // Also get contracts where the application is accepted
            $acceptedContractIds = JobApplication::whereIn('contract_id', $jobIds)
                ->where('status', 'accepted')
                ->pluck('contract_id')
                ->unique();
            
            // Combine all contract IDs where service providers have been accepted
            $validContractIds = $filledContractIds->merge($acceptedContractIds)->unique();
            
            // Only show payments for carers who have been accepted for these contracts
            $acceptedCarerIds = JobApplication::whereIn('contract_id', $validContractIds)
                ->where('status', 'accepted')
                ->pluck('carer_id')
                ->unique();
            
            $query->whereIn('carer_id', $acceptedCarerIds);
        } elseif ($userRole === 'admin') {
            // Admins see all
        } else {
            abort(403, 'Unauthorized');
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('app.pages.timesheet-payments.index', compact('payments'));
    }

    /**
     * View single payment details
     */
    public function show(TimesheetPayment $payment)
    {
        $user = auth()->user();
        $userRole = $user->role->slug ?? '';

        // Check authorization
        if ($userRole === 'client' && $payment->client_id !== $user->id) {
            abort(403, 'Unauthorized');
        } elseif ($userRole !== 'admin' && $userRole !== 'client') {
            abort(403, 'Unauthorized');
        }

        $payment->load(['carer', 'client', 'contract']);
        $timesheets = $payment->timesheets()->get();

        return view('app.pages.timesheet-payments.show', compact('payment', 'timesheets'));
    }

    /**
     * Payment success callback
     */
    public function success(Request $request)
    {
        $paymentId = $request->get('payment_id');
        
        if (!$paymentId) {
            return redirect()->route('dashboard')->with('error', 'Invalid payment reference.');
        }

        $payment = TimesheetPayment::findOrFail($paymentId);
        
        // Mark timesheets as paid
        $this->paymentService->markTimesheetsAsPaid($payment);

        return view('app.pages.timesheet-payments.success', compact('payment'));
    }
}

