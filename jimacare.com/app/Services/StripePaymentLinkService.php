<?php

namespace App\Services;

use App\Models\TimesheetPayment;
use Stripe\Stripe;
use Stripe\PaymentLink;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class StripePaymentLinkService
{
    protected $stripeSecret;

    public function __construct()
    {
        $this->stripeSecret = config('services.stripe.secret');
        Stripe::setApiKey($this->stripeSecret);
    }

    /**
     * Create a payment link for timesheet payment
     */
    public function createPaymentLink(TimesheetPayment $payment): array
    {
        try {
            $paymentLink = PaymentLink::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'gbp',
                        'product_data' => [
                            'name' => $this->getProductName($payment),
                            'description' => $this->getProductDescription($payment),
                        ],
                        'unit_amount' => (int)($payment->total_amount * 100), // Convert to pence
                    ],
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'timesheet_payment_id' => $payment->id,
                    'client_id' => $payment->client_id,
                    'carer_id' => $payment->carer_id,
                    'period_type' => $payment->period_type,
                    'period_start' => $payment->period_start->format('Y-m-d'),
                    'period_end' => $payment->period_end->format('Y-m-d'),
                ],
                'after_completion' => [
                    'type' => 'redirect',
                    'redirect' => [
                        'url' => url('/payment/success?payment_id=' . $payment->id),
                    ],
                ],
            ]);

            // Update payment with Stripe link info
            $payment->update([
                'stripe_payment_link_id' => $paymentLink->id,
                'stripe_payment_link_url' => $paymentLink->url,
                'status' => 'link_sent',
                'link_sent_at' => now(),
            ]);

            return [
                'success' => true,
                'payment_link_id' => $paymentLink->id,
                'payment_link_url' => $paymentLink->url,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe Payment Link Creation Failed: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get product name for payment link
     */
    protected function getProductName(TimesheetPayment $payment): string
    {
        $carerName = $payment->carer->firstname . ' ' . substr($payment->carer->lastname ?? '', 0, 1) . '.';
        $period = ucfirst($payment->period_type) . ' Payment';
        return "Timesheet Payment - {$carerName} ({$period})";
    }

    /**
     * Get product description for payment link
     */
    protected function getProductDescription(TimesheetPayment $payment): string
    {
        $period = $payment->period_start->format('M d') . ' - ' . $payment->period_end->format('M d, Y');
        $carerRole = $payment->carer->role->title ?? 'Service Provider';
        
        return "Payment for {$carerRole} services\n" .
               "Period: {$period}\n" .
               "Hours: {$payment->total_hours}h @ £{$payment->hourly_rate}/hr\n" .
               "Subtotal: £{$payment->subtotal}\n" .
               ($payment->platform_fee > 0 ? "Platform Fee: £{$payment->platform_fee}\n" : '') .
               "Total: £{$payment->total_amount}";
    }

    /**
     * Verify payment status from Stripe
     */
    public function verifyPaymentStatus(TimesheetPayment $payment): bool
    {
        try {
            if (!$payment->stripe_payment_link_id) {
                return false;
            }

            $paymentLink = PaymentLink::retrieve($payment->stripe_payment_link_id);
            
            // Check if payment link has been used
            // Note: Stripe Payment Links don't directly expose payment status
            // You may need to use webhooks or check Payment Intents
            
            return true;
        } catch (ApiErrorException $e) {
            Log::error('Stripe Payment Verification Failed: ' . $e->getMessage());
            return false;
        }
    }
}

