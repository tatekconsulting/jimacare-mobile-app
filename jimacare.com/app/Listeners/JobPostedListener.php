<?php

namespace App\Listeners;

use App\Events\JobPostedEvent;
use App\Models\User;
use App\Notifications\JobPostedNotice;
use App\QueryFilter\SellerLocationFilter;
use App\QueryFilter\SellerTypeFilter;
use App\Services\TwilioService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Queue\InteractsWithQueue;

class JobPostedListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected $twilioService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    /**
     * Handle the event.
     *
     * @param  JobPostedEvent  $event
     * @return void
     */
    public function handle(JobPostedEvent $event)
    {
        $contract = $event->contract;
        $jobRoleId = $contract->role_id; // The role_id of the job (3=Carer, 4=Childminder, 5=Housekeeper)
        
        // Always send email notifications to admins (no location filter)
        $adminUsers = User::where('role_id', 1)
            ->whereNotNull('email')
            ->get();

        foreach ($adminUsers as $admin) {
            try {
                $admin->notify(new JobPostedNotice($contract));
            } catch (\Exception $e) {
                \Log::error('Failed to send job posted email notification to admin', [
                    'user_id' => $admin->id,
                    'contract_id' => $contract->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Prepare location data for filtering service providers by location
        // Only send emails to service providers (carers/childminders/housekeepers) within the job's location radius
        // The radius is specified by the client when posting the job
        
        // Validate that we have location data and radius before filtering
        // Check for non-empty address (not null, not empty string after trim)
        $hasLocationData = !empty(trim($contract->address ?? '')) && 
                          !is_null($contract->lat) && 
                          !is_null($contract->long) && 
                          !is_null($contract->radius) && 
                          $contract->radius > 0 &&
                          $contract->radius <= 100; // Also validate max radius
        
        if (!$hasLocationData) {
            // If no location data or radius, log and skip email notifications to service providers
            \Log::warning('Job posted without complete location data - skipping service provider email notifications', [
                'contract_id' => $contract->id,
                'has_address' => !empty($contract->address),
                'has_lat' => !is_null($contract->lat),
                'has_long' => !is_null($contract->long),
                'radius' => $contract->radius,
            ]);
            
            // Don't send emails to service providers if location/radius is missing
            $serviceProviderUsers = collect([]);
        } else {
            // Prepare location data for filtering
            $data = [
                'address' => $contract->address,
                'lat' => $contract->lat,
                'long' => $contract->long,
                'radius' => $contract->radius, // Use the client-specified radius
                'type' => $contract->role_id, // Filter by job type (3=Carer, 4=Childminder, 5=Housekeeper)
            ];

            // Store original request data and merge location data
            $originalRequest = request()->all();
            request()->merge($data);

            // Get service providers within the job location using the same filters as SMS
            // Only approved and verified sellers (status = active AND approved = true) should receive notifications
            // SellerLocationFilter will filter by distance using the contract's radius
            $serviceProviderUsers = app(Pipeline::class)->send(User::query())->through([
                SellerTypeFilter::class,      // Filter by role type (Carer/Childminder/Housekeeper)
                SellerLocationFilter::class   // Filter by location (within client-specified radius)
            ])->thenReturn()
                ->where('status', 'active')
                ->where('approved', true)
                ->whereNotNull('email')
                ->whereNotNull('lat')
                ->whereNotNull('long')
                ->get();

            // Restore original request data
            request()->merge($originalRequest);
            
            // Log how many service providers are within the radius
            \Log::info('Job posted - service providers filtered by location', [
                'contract_id' => $contract->id,
                'job_role_id' => $jobRoleId,
                'job_location' => $contract->address,
                'radius_miles' => $contract->radius,
                'service_providers_found' => $serviceProviderUsers->count(),
            ]);
        }

        // Send email notifications to service providers within location
        $emailsSent = 0;
        foreach ($serviceProviderUsers as $user) {
            try {
                $user->notify(new JobPostedNotice($contract));
                $emailsSent++;
                
                // Log successful email notification with distance info if available
                if (isset($user->miles)) {
                    \Log::debug('Job posted email sent to service provider', [
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                        'contract_id' => $contract->id,
                        'distance_miles' => $user->miles,
                        'radius_miles' => $contract->radius,
                    ]);
                }
            } catch (\Exception $e) {
                // Log error but continue with other users
                \Log::error('Failed to send job posted email notification to service provider', [
                    'user_id' => $user->id,
                    'contract_id' => $contract->id,
                    'job_role_id' => $jobRoleId,
                    'user_role_id' => $user->role_id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Log summary
        if ($hasLocationData) {
            \Log::info('Job posted email notifications completed', [
                'contract_id' => $contract->id,
                'radius_miles' => $contract->radius,
                'total_service_providers_in_radius' => $serviceProviderUsers->count(),
                'emails_sent' => $emailsSent,
            ]);
        }

        // Send SMS messages to users within job location
        // Use the same location validation as email notifications
        if ($hasLocationData) {
            $data = [
                'address' => $event->contract->address,
                'lat' => $event->contract->lat,
                'long' => $event->contract->long,
                'radius' => $event->contract->radius,
                'type' => $event->contract->role_id,
            ];

            request()->merge($data);

            // Only send SMS to approved sellers (status = active AND approved = true)
            // Exclude pending and review status sellers
            $smsUsers = app(Pipeline::class)->send(User::query())->through([
                SellerTypeFilter::class,
                SellerLocationFilter::class
            ])->thenReturn()
                ->where('status', 'active')
                ->where('approved', true)
                ->whereNotNull('phone')
                ->whereNotNull('lat')
                ->whereNotNull('long')
                ->get();

            $smsSent = 0;
            foreach ($smsUsers as $user) {
                try {
                    // Send SMS notification using secure TwilioService
                    $this->twilioService->sendJobPostedNotification(
                        $user->phone,
                        $event->contract->user->firstname ?? '',
                        $event->contract->role->title ?? '',
                        $event->contract->user->postcode ?? '',
                        route('contract.show', ['contract' => $event->contract->id])
                    );
                    $smsSent++;
                } catch (\Exception $e) {
                    \Log::error('Failed to send SMS notification for job posting', [
                        'user_id' => $user->id,
                        'contract_id' => $event->contract->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            \Log::info('Job posted SMS notifications completed', [
                'contract_id' => $event->contract->id,
                'radius_miles' => $event->contract->radius,
                'sms_sent' => $smsSent,
            ]);
        } else {
            \Log::warning('Skipping SMS notifications - missing location data', [
                'contract_id' => $event->contract->id,
            ]);
        }
    }
}
