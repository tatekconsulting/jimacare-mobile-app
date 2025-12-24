<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class TwilioService
{
    protected $client;
    protected $fromNumber;
    protected $verifySid;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $this->fromNumber = config('services.twilio.from');
        $this->verifySid = config('services.twilio.verify_sid');

        if ($sid && $token) {
            $this->client = new Client($sid, $token);
        }
    }

    /**
     * Send an SMS message
     *
     * @param string $to Phone number to send to
     * @param string $message Message content
     * @return bool
     */
    public function sendSms(string $to, string $message): bool
    {
        if (!$this->client) {
            Log::warning('Twilio client not configured. SMS not sent.');
            return false;
        }

        try {
            $this->client->messages->create($to, [
                'from' => $this->fromNumber,
                'body' => $message
            ]);
            return true;
        } catch (Exception $e) {
            Log::error('Twilio SMS Error: ' . $e->getMessage(), [
                'to' => $to,
                'message' => substr($message, 0, 50) . '...'
            ]);
            return false;
        }
    }

    /**
     * Validate if phone number is a UK number
     *
     * @param string $phoneNumber
     * @return array Returns ['valid' => bool, 'formatted' => string, 'message' => string]
     */
    protected function validateUKPhoneNumber(string $phoneNumber): array
    {
        // Remove all non-numeric characters except +
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        // If it doesn't start with +, try to add UK country code
        if (substr($cleaned, 0, 1) !== '+') {
            // If it starts with 0, replace with +44 (UK)
            if (substr($cleaned, 0, 1) === '0') {
                $cleaned = '+44' . substr($cleaned, 1);
            } elseif (substr($cleaned, 0, 2) === '44') {
                // Already has 44 but missing +
                $cleaned = '+' . $cleaned;
            } else {
                // Assume UK number if no country code
                $cleaned = '+44' . $cleaned;
            }
        }
        
        // Validate it's a UK number (+44)
        if (substr($cleaned, 0, 3) !== '+44') {
            return [
                'valid' => false,
                'formatted' => $cleaned,
                'message' => 'Only UK phone numbers are accepted. Please use a UK number starting with +44 or 0.'
            ];
        }
        
        // UK phone numbers should be 13-14 digits total (+44 + 10-11 digits)
        // Remove +44 to count remaining digits
        $digits = substr($cleaned, 3);
        $digitCount = strlen($digits);
        
        if ($digitCount < 10 || $digitCount > 11) {
            return [
                'valid' => false,
                'formatted' => $cleaned,
                'message' => 'Invalid UK phone number format. UK numbers should be 10-11 digits after the country code (+44).'
            ];
        }
        
        return [
            'valid' => true,
            'formatted' => $cleaned,
            'message' => ''
        ];
    }

    /**
     * Format phone number to E.164 format (UK only)
     *
     * @param string $phoneNumber
     * @return string
     */
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        $validation = $this->validateUKPhoneNumber($phoneNumber);
        return $validation['formatted'];
    }

    /**
     * Send verification code via SMS
     *
     * @param string $phoneNumber
     * @return array Returns ['success' => bool, 'message' => string]
     */
    public function sendVerificationCode(string $phoneNumber): array
    {
        if (!$this->client) {
            $message = 'Twilio client not configured. Please check TWILIO_ACCOUNT_SID and TWILIO_AUTH_TOKEN in .env file.';
            Log::warning($message);
            return ['success' => false, 'message' => $message];
        }

        if (!$this->verifySid) {
            $message = 'Twilio Verification Service not configured. Please check TWILIO_VERIFICATION_SID in .env file.';
            Log::warning($message);
            return ['success' => false, 'message' => $message];
        }

        if (empty($phoneNumber)) {
            $message = 'Phone number is required.';
            Log::warning($message);
            return ['success' => false, 'message' => $message];
        }

        // Validate UK phone number
        $validation = $this->validateUKPhoneNumber($phoneNumber);
        if (!$validation['valid']) {
            Log::warning('Non-UK phone number rejected', [
                'phone' => $phoneNumber,
                'formatted' => $validation['formatted']
            ]);
            return ['success' => false, 'message' => $validation['message']];
        }

        $formattedPhone = $validation['formatted'];

        try {
            $this->client->verify->v2->services($this->verifySid)
                ->verifications
                ->create($formattedPhone, "sms");
            
            Log::info('OTP sent successfully', ['phone' => $formattedPhone]);
            return ['success' => true, 'message' => 'OTP sent successfully'];
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error('Twilio Verification Send Error: ' . $errorMessage, [
                'phone' => $phoneNumber,
                'formatted_phone' => $formattedPhone ?? null
            ]);
            
            // Provide user-friendly error messages
            if (strpos($errorMessage, 'Invalid phone number') !== false) {
                $message = 'Invalid UK phone number format. Please ensure it is a valid UK number (e.g., +44 7700 900000 or 07700 900000).';
            } elseif (strpos($errorMessage, 'not found') !== false) {
                $message = 'Twilio service configuration error. Please contact support.';
            } else {
                $message = 'Failed to send OTP. Please ensure you are using a valid UK phone number.';
            }
            
            return ['success' => false, 'message' => $message];
        }
    }

    /**
     * Verify the OTP code
     *
     * @param string $phoneNumber
     * @param string $code
     * @return bool
     */
    public function verifyCode(string $phoneNumber, string $code): bool
    {
        if (!$this->client || !$this->verifySid) {
            Log::warning('Twilio verification not configured.');
            return false;
        }

        try {
            // Format phone number to E.164 format
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            
            $verification = $this->client->verify->v2->services($this->verifySid)
                ->verificationChecks
                ->create(['code' => $code, 'to' => $formattedPhone]);

            return $verification->valid;
        } catch (Exception $e) {
            Log::error('Twilio Verification Check Error: ' . $e->getMessage(), [
                'phone' => $phoneNumber,
                'formatted_phone' => $formattedPhone ?? null
            ]);
            return false;
        }
    }

    /**
     * Send new message notification
     *
     * @param string $to
     * @param string $senderName
     * @return bool
     */
    public function sendNewMessageNotification(string $to, string $senderName): bool
    {
        $message = "You have received a new message from {$senderName} at Jimacare";
        return $this->sendSms($to, $message);
    }

    /**
     * Send job posted notification
     *
     * @param string $to
     * @param string $posterName
     * @param string $roleTitle
     * @param string $postcode
     * @param string $jobUrl
     * @return bool
     */
    public function sendJobPostedNotification(
        string $to,
        string $posterName,
        string $roleTitle,
        string $postcode,
        string $jobUrl
    ): bool {
        $message = "{$posterName} is Looking for a {$roleTitle} in {$postcode}. " .
            "You can see details here {$jobUrl}. Thank you for using our application!";
        return $this->sendSms($to, $message);
    }

    /**
     * Check if Twilio is configured
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        return $this->client !== null;
    }
}

