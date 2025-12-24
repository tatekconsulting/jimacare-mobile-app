<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Models\Inbox;
use App\Models\Invoice;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware(['auth', 'verified']);
	}

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
	    $auth = auth()->user();
	    $inbox = Inbox::where(function ($q) use($auth, $user){
		    return $q->where('client_id', $auth->id)->where('seller_id', $user->id);
	    })->orWhere(function($q) use ($auth, $user){
		    return $q->where('seller_id', $auth->id)->where('client_id', $user->id);
	    })->first();

	    if (!$inbox){
			$inbox= Inbox::create(['client_id'=> $auth->id,'seller_id'=> $user->id]);
		}
	    $message = $inbox->messages()->create([
	    	'from_id'   => $auth->id,
		    'message'   => $request->message
	    ]);

	    broadcast(new MessageEvent($auth, $user, $message));

	    return [
	    	'id'        => $user->id,
		    'name'      => ($auth->firstname ?? '') . '' . ($auth->lastname[0] ?? ''),
		    'profile'   => asset($auth->profile ?? 'img/undraw_profile.svg'),
		    'type'      => $message->type,
		    'message'   => $message->message ?? '',
		    'sent_at'   => $message->created_at->format('d/m/Y \a\t H:i')
	    ];
    }

    public function invoice(Request $request, User $user){
	    $auth = auth()->user();
	    $inbox = Inbox::where(function ($q) use($auth, $user){
		    return $q->where('client_id', $auth->id)->where('seller_id', $user->id);
	    })->orWhere(function($q) use ($auth, $user){
		    return $q->where('seller_id', $auth->id)->where('client_id', $user->id);
	    })->firstOrFail();

	    $message = $inbox->messages()->create([
		    'from_id'   => $auth->id,
		    'message'   => $request->message,
		    'type'      => 'invoice'
	    ]);

	    $invoice = Invoice::create([
	    	'message_id'    => $message->id,
	    	'price'         => $request->price
	    ]);


	    broadcast(new MessageEvent($auth, $user, $message));

	    return [
		    'id'        => $user->id,
		    'name'      => ($auth->firstname ?? '') . '' . ($auth->lastname[0] ?? ''),
		    'type'      => $message->type,
		    'profile'   => asset($auth->profile ?? 'img/undraw_profile.svg'),
		    'message'   => $message->message ?? '',
		    'invoice'   => [
		    	'price' => $invoice->price,
			    'active' => true,
			    'paid'  => false,
			    'cancel'    => route('invoice.cancel', ['invoice' => $invoice->id])
		    ],
		    'sent_at'   => $message->created_at->format('d/m/Y \a\t H:i')
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
