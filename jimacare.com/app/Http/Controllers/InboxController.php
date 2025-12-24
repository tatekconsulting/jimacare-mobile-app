<?php

namespace App\Http\Controllers;

use App\Models\Inbox;
use App\Models\User;
use Illuminate\Http\Request;

class InboxController extends Controller
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
        $auth = auth()->user();
	    $inboxes = Inbox::where('client_id', $auth->id)
		    ->orWhere('seller_id', $auth->id)
		    ->get()
	    ;
    	return view('app.pages.inbox', compact('inboxes', 'auth'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($user)
    {
	    $auth = auth()->user();
	    
	    // Handle both User model (route model binding) and user ID
	    if (!($user instanceof User)) {
		    $user = User::find($user);
		    
		    // If user doesn't exist, redirect with error
		    if (!$user) {
			    return redirect()->route('inbox')->with('error', 'User not found.');
		    }
	    }
	    
	    // Prevent users from messaging themselves
	    if ($auth->id == $user->id) {
		    return redirect()->route('inbox')->with('error', 'You cannot message yourself.');
	    }
	    
	    $inboxes = Inbox::where('client_id', $auth->id)
		    ->orWhere('seller_id', $auth->id)
		    ->get()
	    ;

	    // Find or create inbox between the two users
	    $inbox = Inbox::where(function ($q) use($auth, $user){
    		return $q->where('client_id', $auth->id)->where('seller_id', $user->id);
	    })->orWhere(function($q) use ($auth, $user){
		    return $q->where('seller_id', $auth->id)->where('client_id', $user->id);
	    })->first();

	    // Create inbox if it doesn't exist
	    if (!$inbox) {
		    $inbox = Inbox::create([
			    'client_id' => $auth->id,
			    'seller_id' => $user->id
		    ]);
		    
		    // Refresh the inboxes list
		    $inboxes = Inbox::where('client_id', $auth->id)
			    ->orWhere('seller_id', $auth->id)
			    ->get();
	    }

	    // Load messages with relationships and order by created_at
	    $messages = $inbox->messages()
	        ->with(['from', 'invoice'])
	        ->orderBy('created_at', 'asc')
	        ->get();

    	return view('app.pages.inbox', compact('auth', 'user', 'inboxes', 'inbox', 'messages'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inbox  $inbox
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inbox $inbox)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inbox  $inbox
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inbox $inbox)
    {
        //
    }
}
