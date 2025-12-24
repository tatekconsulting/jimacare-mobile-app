<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;

class VideoCallController extends Controller
{
    protected $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        // Remove auth middleware from constructor since it's handled by route middleware
        // This allows both web and API routes to use this controller
        $this->pushService = $pushService;
    }

    /**
     * Show the video call page
     */
    public function show(Request $request)
    {
        $request->validate([
            'room' => 'required|string'
        ]);

        $user = auth()->user();
        $roomId = $request->query('room');
        $token = $request->query('token');

        // Verify user has access to this room
        $call = DB::table('video_calls')
            ->where('room_id', $roomId)
            ->where(function ($q) use ($user) {
                $q->where('initiated_by', $user->id)
                  ->orWhere('recipient_id', $user->id);
            })
            ->first();

        if (!$call) {
            abort(404, 'Video call not found or you do not have access to this call.');
        }

        // If no token provided, generate one for the current user
        if (!$token) {
            try {
                \Log::info('Generating Twilio token for video call', [
                    'user_id' => $user->id,
                    'room_id' => $roomId
                ]);
                
                $token = $this->generateTwilioToken($user, $roomId);
                
                if (empty($token)) {
                    throw new \Exception('Token generation returned empty string');
                }
                
                \Log::info('Twilio token generated successfully', [
                    'user_id' => $user->id,
                    'room_id' => $roomId,
                    'token_length' => strlen($token),
                    'token_preview' => substr($token, 0, 20) . '...'
                ]);
                
                // Update call status if recipient is joining
                if ($call->recipient_id === $user->id && $call->status === 'pending') {
                    DB::table('video_calls')
                        ->where('id', $call->id)
                        ->update([
                            'status' => 'active',
                            'started_at' => now(),
                            'updated_at' => now()
                        ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to generate token for video call', [
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e),
                    'user_id' => $user->id,
                    'room_id' => $roomId,
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Return a more helpful error page
                return view('app.pages.video-call-error', [
                    'error' => 'Failed to generate video call token',
                    'message' => $e->getMessage(),
                    'roomId' => $roomId,
                    'suggestion' => 'Please check Twilio API credentials in .env file and ensure TWILIO_API_KEY and TWILIO_API_SECRET are set correctly.'
                ]);
            }
        } else {
            // Validate provided token
            if (strlen($token) < 50 || !str_starts_with($token, 'eyJ')) {
                \Log::warning('Invalid token provided in URL', [
                    'user_id' => $user->id,
                    'room_id' => $roomId,
                    'token_length' => strlen($token),
                    'token_preview' => substr($token, 0, 20)
                ]);
                // Regenerate token
                try {
                    $token = $this->generateTwilioToken($user, $roomId);
                } catch (\Exception $e) {
                    \Log::error('Failed to regenerate token', ['error' => $e->getMessage()]);
                }
            }
        }

        // Get the other participant
        $otherUserId = $call->initiated_by === $user->id ? $call->recipient_id : $call->initiated_by;
        $otherUser = User::find($otherUserId);

        if (!$otherUser) {
            abort(404, 'Other participant not found.');
        }

        return view('app.pages.video-call', [
            'roomId' => $roomId,
            'token' => $token,
            'otherUser' => $otherUser,
            'callId' => $call->id
        ]);
    }

    /**
     * Initiate a video call
     */
    public function initiate(Request $request, User $user)
    {
        $request->validate([
            'booking_id' => 'nullable|integer|exists:instant_bookings,id'
        ]);

        $caller = auth()->user();

        // Generate unique room ID
        $roomId = 'jimacare-' . Str::random(12);

        // Create video call record
        $call = DB::table('video_calls')->insertGetId([
            'room_id' => $roomId,
            'initiated_by' => $caller->id,
            'recipient_id' => $user->id,
            'booking_id' => $request->booking_id,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        try {
            // Generate Twilio access token for caller
            $token = $this->generateTwilioToken($caller, $roomId);

            // Send push notification to recipient
            try {
                $this->pushService->sendToUser(
                    $user,
                    '📹 Incoming Video Call',
                    "{$caller->name} is calling you",
                    [
                        'url' => route('video.show', ['room' => $roomId]),
                        'tag' => 'video-call-' . $call,
                        'actions' => [
                            ['action' => 'answer', 'title' => 'Answer'],
                            ['action' => 'decline', 'title' => 'Decline']
                        ]
                    ]
                );
            } catch (\Exception $e) {
                // Log but don't fail the call if push notification fails
                \Log::warning('Failed to send push notification for video call', [
                    'error' => $e->getMessage(),
                    'call_id' => $call
                ]);
            }

            return response()->json([
                'success' => true,
                'call_id' => $call,
                'room_id' => $roomId,
                'token' => $token,
                'recipient' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'profile' => asset($user->profile ?? 'img/undraw_profile.svg')
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to initiate video call', [
                'error' => $e->getMessage(),
                'caller_id' => $caller->id,
                'recipient_id' => $user->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate video call: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Join an existing video call
     */
    public function join(Request $request, $roomId)
    {
        $user = auth()->user();

        $call = DB::table('video_calls')
            ->where('room_id', $roomId)
            ->whereIn('status', ['pending', 'active'])
            ->where(function ($q) use ($user) {
                $q->where('initiated_by', $user->id)
                  ->orWhere('recipient_id', $user->id);
            })
            ->first();

        if (!$call) {
            return response()->json([
                'success' => false,
                'message' => 'Call not found or has ended'
            ], 404);
        }

        // Update call status if recipient is joining
        if ($call->recipient_id === $user->id && $call->status === 'pending') {
            DB::table('video_calls')
                ->where('id', $call->id)
                ->update([
                    'status' => 'active',
                    'started_at' => now(),
                    'updated_at' => now()
                ]);
        }

        try {
            $token = $this->generateTwilioToken($user, $roomId);

            $otherUserId = $call->initiated_by === $user->id ? $call->recipient_id : $call->initiated_by;
            $otherUser = User::find($otherUserId);

            if (!$otherUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Other participant not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'room_id' => $roomId,
                'token' => $token,
                'other_participant' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'profile' => asset($otherUser->profile ?? 'img/undraw_profile.svg')
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to join video call', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'room_id' => $roomId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to join video call: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * End a video call
     */
    public function end(Request $request, $roomId)
    {
        $user = auth()->user();

        $call = DB::table('video_calls')
            ->where('room_id', $roomId)
            ->where(function ($q) use ($user) {
                $q->where('initiated_by', $user->id)
                  ->orWhere('recipient_id', $user->id);
            })
            ->first();

        if (!$call) {
            return response()->json([
                'success' => false,
                'message' => 'Call not found'
            ], 404);
        }

        $duration = $call->started_at 
            ? now()->diffInSeconds($call->started_at) 
            : 0;

        DB::table('video_calls')
            ->where('id', $call->id)
            ->update([
                'status' => 'ended',
                'ended_at' => now(),
                'duration_seconds' => $duration,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Call ended',
            'duration_seconds' => $duration
        ]);
    }

    /**
     * Diagnostic endpoint to check Twilio configuration
     */
    public function diagnose()
    {
        $accountSid = config('services.twilio.sid');
        $apiKey = config('services.twilio.api_key');
        $apiSecret = config('services.twilio.api_secret');
        
        $diagnostics = [
            'account_sid' => [
                'configured' => !empty($accountSid),
                'format_valid' => !empty($accountSid) && str_starts_with($accountSid, 'AC') && strlen($accountSid) >= 30,
                'length' => strlen($accountSid ?? ''),
                'preview' => !empty($accountSid) ? substr($accountSid, 0, 10) . '...' : 'not set'
            ],
            'api_key' => [
                'configured' => !empty($apiKey),
                'format_valid' => !empty($apiKey) && str_starts_with($apiKey, 'SK') && strlen($apiKey) >= 30,
                'length' => strlen($apiKey ?? ''),
                'preview' => !empty($apiKey) ? substr($apiKey, 0, 10) . '...' : 'not set'
            ],
            'api_secret' => [
                'configured' => !empty($apiSecret),
                'format_valid' => !empty($apiSecret) && strlen($apiSecret) >= 30,
                'length' => strlen($apiSecret ?? ''),
                'preview' => !empty($apiSecret) ? '***' . substr($apiSecret, -4) : 'not set'
            ],
            'all_configured' => !empty($accountSid) && !empty($apiKey) && !empty($apiSecret),
            'can_generate_token' => false,
            'test_token' => null,
            'test_error' => null
        ];
        
        // Try to generate a test token
        if ($diagnostics['all_configured']) {
            try {
                $testUser = auth()->user() ?? User::first();
                if ($testUser) {
                    $testToken = $this->generateTwilioToken($testUser, 'test-room-' . time());
                    $diagnostics['can_generate_token'] = true;
                    $diagnostics['test_token'] = substr($testToken, 0, 50) . '... (length: ' . strlen($testToken) . ')';
                }
            } catch (\Exception $e) {
                $diagnostics['test_error'] = $e->getMessage();
            }
        }
        
        // Return HTML view if requested via browser, JSON if API request
        if (request()->wantsJson() || request()->expectsJson()) {
            return response()->json($diagnostics);
        }
        
        return view('app.pages.video-call-diagnose', compact('diagnostics'));
    }

    /**
     * Decline an incoming call
     */
    public function decline(Request $request, $roomId)
    {
        $user = auth()->user();

        DB::table('video_calls')
            ->where('room_id', $roomId)
            ->where('recipient_id', $user->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'missed',
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Call declined'
        ]);
    }

    /**
     * Generate Twilio access token for video
     */
    private function generateTwilioToken(User $user, string $roomId): string
    {
        $accountSid = config('services.twilio.sid');
        $apiKey = config('services.twilio.api_key');
        $apiSecret = config('services.twilio.api_secret');

        // If Twilio Video is not configured, log error and throw exception
        if (!$accountSid || !$apiKey || !$apiSecret) {
            \Log::error('Twilio Video credentials not configured', [
                'has_sid' => !empty($accountSid),
                'has_api_key' => !empty($apiKey),
                'has_api_secret' => !empty($apiSecret)
            ]);
            throw new \Exception('Twilio Video is not configured. Please contact administrator.');
        }

        try {
            // Validate credentials format
            if (strlen($accountSid) < 30 || !str_starts_with($accountSid, 'AC')) {
                throw new \Exception('Invalid Account SID format. Must start with AC and be at least 30 characters.');
            }
            
            if (strlen($apiKey) < 30 || !str_starts_with($apiKey, 'SK')) {
                throw new \Exception('Invalid API Key format. Must start with SK and be at least 30 characters.');
            }
            
            if (strlen($apiSecret) < 30) {
                throw new \Exception('Invalid API Secret format. Must be at least 30 characters.');
            }

            // Identity must only contain alphanumeric and underscore characters
            // Format: user_id_randomstring (e.g., "123_abc12345")
            $identity = $user->id . '_' . Str::random(8);
            
            // Validate identity format (alphanumeric and underscore only)
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $identity)) {
                throw new \Exception('Invalid identity format. Identity must contain only alphanumeric and underscore characters.');
            }
            
            $token = new AccessToken(
                $accountSid,
                $apiKey,
                $apiSecret,
                3600, // TTL in seconds
                $identity
            );

            $videoGrant = new VideoGrant();
            $videoGrant->setRoom($roomId);
            $token->addGrant($videoGrant);

            $jwtToken = $token->toJWT();
            
            // Validate token was generated
            if (empty($jwtToken) || strlen($jwtToken) < 50) {
                throw new \Exception('Generated token is invalid or too short.');
            }
            
            \Log::info('Twilio token generated successfully', [
                'user_id' => $user->id,
                'room_id' => $roomId,
                'identity' => $identity,
                'token_length' => strlen($jwtToken)
            ]);

            return $jwtToken;
        } catch (\Exception $e) {
            \Log::error('Failed to generate Twilio token', [
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'user_id' => $user->id,
                'room_id' => $roomId,
                'account_sid_length' => strlen($accountSid ?? ''),
                'api_key_length' => strlen($apiKey ?? ''),
                'api_secret_length' => strlen($apiSecret ?? '')
            ]);
            throw new \Exception('Failed to generate video call token: ' . $e->getMessage());
        }
    }
}

