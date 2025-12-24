<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Contract;
use App\Models\Inbox;
use App\Models\Invoice;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BookingApiController extends Controller
{
    protected $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->middleware('auth');
        $this->pushService = $pushService;
    }

    /**
     * Create instant booking request
     */
    public function create(Request $request, User $user)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'service_type' => 'nullable|integer|exists:types,id',
            'message' => 'nullable|string|max:500',
            'hourly_rate' => 'nullable|numeric|min:0'
        ]);

        $client = auth()->user();

        // Validate user is a service provider
        if (!in_array($user->role_id, [3, 4, 5])) {
            return response()->json([
                'success' => false,
                'message' => 'User is not a service provider'
            ], 400);
        }

        // Check if user is available
        if (!$user->available_now && !$this->isUserAvailable($user, $request->date, $request->start_time, $request->end_time)) {
            return response()->json([
                'success' => false,
                'message' => 'Carer is not available at the requested time'
            ], 409);
        }

        DB::beginTransaction();
        try {
            // Create or get inbox
            $inbox = Inbox::firstOrCreate(
                [
                    'client_id' => $client->id,
                    'seller_id' => $user->id
                ]
            );

            // Calculate estimated price
            $hours = Carbon::parse($request->start_time)->diffInHours(Carbon::parse($request->end_time));
            $hourlyRate = $request->hourly_rate ?? $user->fee ?? 15;
            $estimatedPrice = $hours * $hourlyRate;

            // Create booking message
            $bookingDetails = "📅 Booking Request\n" .
                "Date: " . Carbon::parse($request->date)->format('l, F j, Y') . "\n" .
                "Time: {$request->start_time} - {$request->end_time}\n" .
                "Duration: {$hours} hours\n" .
                "Estimated: £" . number_format($estimatedPrice, 2);

            if ($request->message) {
                $bookingDetails .= "\n\nMessage: {$request->message}";
            }

            $message = $inbox->messages()->create([
                'from_id' => $client->id,
                'message' => $bookingDetails,
                'type' => 'booking_request'
            ]);

            // Create pending invoice
            $invoice = Invoice::create([
                'message_id' => $message->id,
                'from_id' => $user->id,
                'to_id' => $client->id,
                'price' => $estimatedPrice,
                'status' => 'pending_acceptance'
            ]);

            // Store booking metadata
            $booking = DB::table('instant_bookings')->insertGetId([
                'invoice_id' => $invoice->id,
                'client_id' => $client->id,
                'carer_id' => $user->id,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'hourly_rate' => $hourlyRate,
                'estimated_price' => $estimatedPrice,
                'status' => 'pending',
                'expires_at' => now()->addMinutes(30),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            // Send push notification to carer
            $this->pushService->sendToUser(
                $user,
                '🔔 New Booking Request!',
                "{$client->name} wants to book you for " . Carbon::parse($request->date)->format('M j'),
                [
                    'url' => route('inbox.show', ['user' => $client->id]),
                    'tag' => 'booking-request-' . $booking
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Booking request sent! The carer has 30 minutes to respond.',
                'booking' => [
                    'id' => $booking,
                    'invoice_id' => $invoice->id,
                    'status' => 'pending',
                    'expires_at' => now()->addMinutes(30)->toIso8601String(),
                    'estimated_price' => $estimatedPrice
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking request'
            ], 500);
        }
    }

    /**
     * Get booking status
     */
    public function status($bookingId)
    {
        $booking = DB::table('instant_bookings')
            ->where('id', $bookingId)
            ->where(function ($q) {
                $q->where('client_id', auth()->id())
                  ->orWhere('carer_id', auth()->id());
            })
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        // Check if expired
        if ($booking->status === 'pending' && Carbon::parse($booking->expires_at)->isPast()) {
            DB::table('instant_bookings')
                ->where('id', $bookingId)
                ->update(['status' => 'expired', 'updated_at' => now()]);
            $booking->status = 'expired';
        }

        return response()->json([
            'success' => true,
            'booking' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'date' => $booking->date,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'estimated_price' => $booking->estimated_price,
                'expires_at' => $booking->expires_at,
                'is_expired' => Carbon::parse($booking->expires_at)->isPast()
            ]
        ]);
    }

    /**
     * Accept booking (carer only)
     */
    public function accept(Request $request, $bookingId)
    {
        $user = auth()->user();

        $booking = DB::table('instant_bookings')
            ->where('id', $bookingId)
            ->where('carer_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or already processed'
            ], 404);
        }

        // Check if expired
        if (Carbon::parse($booking->expires_at)->isPast()) {
            DB::table('instant_bookings')
                ->where('id', $bookingId)
                ->update(['status' => 'expired', 'updated_at' => now()]);

            return response()->json([
                'success' => false,
                'message' => 'Booking request has expired'
            ], 410);
        }

        DB::table('instant_bookings')
            ->where('id', $bookingId)
            ->update([
                'status' => 'accepted',
                'accepted_at' => now(),
                'updated_at' => now()
            ]);

        // Update invoice status
        Invoice::where('id', $booking->invoice_id)
            ->update(['status' => 'pending']);

        // Notify client
        $client = User::find($booking->client_id);
        $this->pushService->sendToUser(
            $client,
            '✅ Booking Accepted!',
            "{$user->name} accepted your booking request",
            [
                'url' => route('invoice.pay', ['invoice' => $booking->invoice_id]),
                'tag' => 'booking-accepted-' . $bookingId
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Booking accepted! Waiting for client payment.',
            'payment_url' => route('invoice.pay', ['invoice' => $booking->invoice_id])
        ]);
    }

    /**
     * Decline booking (carer only)
     */
    public function decline(Request $request, $bookingId)
    {
        $request->validate([
            'reason' => 'nullable|string|max:255'
        ]);

        $user = auth()->user();

        $booking = DB::table('instant_bookings')
            ->where('id', $bookingId)
            ->where('carer_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or already processed'
            ], 404);
        }

        DB::table('instant_bookings')
            ->where('id', $bookingId)
            ->update([
                'status' => 'declined',
                'decline_reason' => $request->reason,
                'updated_at' => now()
            ]);

        // Update invoice status
        Invoice::where('id', $booking->invoice_id)
            ->update(['status' => 'cancelled']);

        // Notify client
        $client = User::find($booking->client_id);
        $this->pushService->sendToUser(
            $client,
            'Booking Declined',
            "{$user->name} couldn't accept your booking request",
            [
                'url' => route('sellers'),
                'tag' => 'booking-declined-' . $bookingId
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Booking declined'
        ]);
    }

    /**
     * Check if user is available for a specific time slot
     */
    private function isUserAvailable(User $user, $date, $startTime, $endTime): bool
    {
        // Check existing bookings
        $hasConflict = DB::table('instant_bookings')
            ->where('carer_id', $user->id)
            ->where('date', $date)
            ->where('status', 'accepted')
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function ($q2) use ($startTime, $endTime) {
                      $q2->where('start_time', '<=', $startTime)
                         ->where('end_time', '>=', $endTime);
                  });
            })
            ->exists();

        return !$hasConflict;
    }
}

