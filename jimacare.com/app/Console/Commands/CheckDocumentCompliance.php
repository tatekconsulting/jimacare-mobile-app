<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Notifications\CertificationExpiringAdmin;
use Carbon\Carbon;

class CheckDocumentCompliance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compliance:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check document compliance status and send alerts for expiring/expired documents';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking document compliance...');

        // Only check documents for Carers (role_id 3), Childminders (role_id 4), and Housekeepers (role_id 5)
        $serviceProviderRoleIds = [3, 4, 5];
        
        // Get documents for service providers only
        $documents = Document::whereNotNull('expiration')
            ->whereHas('user', function ($query) use ($serviceProviderRoleIds) {
                $query->whereIn('role_id', $serviceProviderRoleIds);
            })
            ->with('user.role')
            ->get();

        $updated = 0;
        $expiringCount = 0;
        $expiredCount = 0;
        $adminNotificationsSent = 0;

        foreach ($documents as $document) {
            $oldStatus = $document->compliance_status;
            $document->updateComplianceStatus();
            
            if ($oldStatus !== $document->compliance_status) {
                $document->save();
                $updated++;
            }

            // Check for expiring documents (within 30 days, not expired)
            if ($document->isExpiringSoon() && !$document->isExpired()) {
                $expiringCount++;
                
                // Calculate days until expiry
                $daysUntilExpiry = now()->diffInDays($document->expiration, false);
                
                // Only notify admins if document is expiring within 30 days
                if ($daysUntilExpiry > 0 && $daysUntilExpiry <= 30) {
                    try {
                        // Get all admin users (role_id = 1)
                        $admins = User::where('role_id', 1)
                            ->whereNotNull('email')
                            ->get();
                        
                        $userName = trim(($document->user->firstname ?? '') . ' ' . ($document->user->lastname ?? ''));
                        $userRole = $document->user->role->title ?? 'Service Provider';
                        $documentName = $document->name ?? 'Certification';
                        $expirationDate = $document->expiration ? $document->expiration->format('d/m/Y') : 'N/A';
                        $daysText = $daysUntilExpiry == 1 ? 'day' : 'days';
                        
                        // Send notification to each admin
                        foreach ($admins as $admin) {
                            // Send email notification
                            try {
                                $admin->notify(new CertificationExpiringAdmin($document, $daysUntilExpiry));
                            } catch (\Exception $e) {
                                Log::warning('Failed to send email notification to admin', [
                                    'admin_id' => $admin->id,
                                    'document_id' => $document->id,
                                    'error' => $e->getMessage()
                                ]);
                            }
                            
                            // Create in-app notification
                            try {
                                UserNotification::create([
                                    'user_id' => $admin->id,
                                    'type' => 'certification_expiring',
                                    'title' => "Certification Expiring: {$documentName}",
                                    'message' => "{$userName} ({$userRole}) has a certification '{$documentName}' expiring in {$daysUntilExpiry} {$daysText} (Expires: {$expirationDate})",
                                    'action_url' => url('/dashboard/user/' . $document->user->id),
                                    'data' => [
                                        'document_id' => $document->id,
                                        'user_id' => $document->user->id,
                                        'user_name' => $userName,
                                        'user_role' => $userRole,
                                        'document_name' => $documentName,
                                        'expiration_date' => $expirationDate,
                                        'days_until_expiry' => $daysUntilExpiry,
                                    ],
                                    'is_read' => false,
                                ]);
                            } catch (\Exception $e) {
                                Log::warning('Failed to create in-app notification for admin', [
                                    'admin_id' => $admin->id,
                                    'document_id' => $document->id,
                                    'error' => $e->getMessage()
                                ]);
                            }
                            
                            $adminNotificationsSent++;
                        }
                        
                        $this->info("Notified admins about expiring certification: {$documentName} for user {$document->user->email}");
                    } catch (\Exception $e) {
                        Log::error('Failed to send admin notification for expiring certification', [
                            'document_id' => $document->id,
                            'user_id' => $document->user_id,
                            'error' => $e->getMessage()
                        ]);
                        $this->error("Failed to notify admins for document ID: {$document->id}");
                    }
                }
            }

            // Check for expired documents
            if ($document->isExpired()) {
                $expiredCount++;
                // You can add expired notification logic here if needed
            }
        }

        $this->info("Compliance check complete!");
        $this->info("Updated: {$updated} documents");
        $this->info("Expiring soon: {$expiringCount} documents");
        $this->info("Expired: {$expiredCount} documents");
        $this->info("Admin notifications sent: {$adminNotificationsSent}");

        return 0;
    }
}

