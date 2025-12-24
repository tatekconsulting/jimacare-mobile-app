<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Models\Inbox;
use App\Models\Invoice;
use App\Models\Message;
use App\Models\User;
use App\Services\TwilioService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
	protected $twilioService;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(TwilioService $twilioService)
	{
		$this->middleware(['auth', 'verified']);
		$this->twilioService = $twilioService;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request, User $user)
	{
		try {
			// Validate the request
			$request->validate([
				'message' => 'required|string|max:5000'
			]);

			$auth = auth()->user();
			
			// Prevent users from messaging themselves
			if ($auth->id == $user->id) {
				return response()->json([
					'success' => false,
					'message' => 'You cannot message yourself.'
				], 400);
			}

			$inbox = Inbox::where(function ($q) use ($auth, $user) {
				return $q->where('client_id', $auth->id)->where('seller_id', $user->id);
			})->orWhere(function ($q) use ($auth, $user) {
				return $q->where('seller_id', $auth->id)->where('client_id', $user->id);
			})->first();

			if (!$inbox) {
				$inbox = Inbox::create(['client_id' => $auth->id, 'seller_id' => $user->id]);

				// Send SMS notification using secure TwilioService (only if phone exists)
				if ($user->phone) {
					try {
						$senderName = ($auth->firstname ?? '') . ' ' . ($auth->lastname ?? '');
						$this->twilioService->sendNewMessageNotification($user->phone, $senderName);
					} catch (\Exception $e) {
						// Log but don't fail the message if SMS fails
						\Log::warning('Failed to send SMS notification: ' . $e->getMessage());
					}
				}
			}

			$message = $inbox->messages()->create([
				'from_id' => $auth->id,
				'message' => $request->message,
				'type' => 'text'
			]);

			// Broadcast the message event
			try {
				broadcast(new MessageEvent($auth, $user, $message));
			} catch (\Exception $e) {
				// Log but don't fail the message if broadcast fails
				\Log::warning('Failed to broadcast message: ' . $e->getMessage());
			}

			return response()->json([
				'success' => true,
				'id' => $user->id,
				'name' => ($auth->firstname ?? '') . ($auth->lastname ? ' ' . substr($auth->lastname, 0, 1) : ''),
				'profile' => asset($auth->profile ?? 'img/undraw_profile.svg'),
				'type' => $message->type ?? 'text',
				'message' => $message->message ?? '',
				'sent_at' => $message->created_at->format('d/m/Y \a\t H:i')
			]);

		} catch (\Illuminate\Validation\ValidationException $e) {
			return response()->json([
				'success' => false,
				'message' => 'Validation failed: ' . $e->getMessage(),
				'errors' => $e->errors()
			], 422);
		} catch (\Exception $e) {
			\Log::error('Message send error: ' . $e->getMessage(), [
				'exception' => $e,
				'user_id' => auth()->id(),
				'recipient_id' => $user->id ?? null
			]);

			return response()->json([
				'success' => false,
				'message' => 'Failed to send message. Please try again.'
			], 500);
		}
	}

	public function invoice(Request $request, User $user)
	{
		$auth = auth()->user();
		$inbox = Inbox::where(function ($q) use ($auth, $user) {
			return $q->where('client_id', $auth->id)->where('seller_id', $user->id);
		})->orWhere(function ($q) use ($auth, $user) {
			return $q->where('seller_id', $auth->id)->where('client_id', $user->id);
		})->firstOrFail();

		$message = $inbox->messages()->create([
			'from_id' => $auth->id,
			'message' => $request->message,
			'type' => 'invoice'
		]);

		$invoice = Invoice::create([
			'message_id' => $message->id,
			'price' => $request->price
		]);


		broadcast(new MessageEvent($auth, $user, $message));

		return [
			'id' => $user->id,
			'name' => ($auth->firstname ?? '') . ($auth->lastname ? ' ' . substr($auth->lastname, 0, 1) : ''),
			'type' => $message->type ?? 'invoice',
			'profile' => asset($auth->profile ?? 'img/undraw_profile.svg'),
			'message' => $message->message ?? '',
			'invoice' => [
				'price' => $invoice->price,
				'active' => true,
				'paid' => false,
				'cancel' => route('invoice.cancel', ['invoice' => $invoice->id])
			],
			'sent_at' => $message->created_at->format('d/m/Y \a\t H:i')
		];
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Message  $message
	 * @return \Illuminate\Http\Response
	 */
	public function show(Message $message)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Models\Message  $message
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Message $message)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Models\Message  $message
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Message $message)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Message  $message
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Message $message)
	{
		//
	}
}
