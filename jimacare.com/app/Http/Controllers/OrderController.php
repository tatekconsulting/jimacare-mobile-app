<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OrderController extends Controller
{

	public function __construct(){
		$this->middleware('auth');
	}

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->id();
    	$orders = Order::where( 'client_id', $user )
		    ->orWhere('seller_id', $user)
	        ->get()
	    ;
    	return view('app.pages.order.index', compact('orders'));
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
    	$user = auth()->user();

    	if( !(in_array($user->id, [$order->client_id ?? '', $order->seller_id ?? '']) || ($user->role->slug == 'admin')) ){
		    return abort(404);
	    }

    	return view('app.pages.order.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }

    public function review(Request $request, Order $order){
	    $user = auth()->id();

    	if( ($order->client_id != $user) || !($order->invoice->id ?? false) ) {
		    return abort(404);
	    }

    	$review = Review::create([
    		'order_id'  => $order->id,
		    'client_id' => $order->client_id,
		    'seller_id' => $order->seller_id,
		    'stars'    => $request->stars,
		    'desc'      => $request->desc
	    ]);

    	return back();
    }

    public function submit(Request $request, Order $order){
	    $user = auth()->id();
	    if( $order->seller_id != $user ) {
		    return abort(404);
	    }

    	$order->update([
    		'status' => 'submitted'
	    ]);

    	return back();
    }

	public function revision(Request $request, Order $order){

		$user = auth()->id();
		if( $order->client_id != $user ) {
			return abort(404);
		}

		$order->update([
			'status' => 'revision'
		]);

		return back();
	}

	public function complete(Request $request, Order $order){

		$user = auth()->id();
		if( $order->client_id != $user ) {
			return abort(404);
		}

		$order->update([
			'status' => 'completed',
			'completed_at' => Carbon::now()->toDateTimeString(),
		]);

		return back();
	}



}
