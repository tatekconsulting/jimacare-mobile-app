<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Stripe\Charge;
use Stripe\Stripe;

class InvoiceController extends Controller
{
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    public function pay(Invoice $invoice){
    	return view('app.pages.invoice.payment', compact('invoice'));
    }

    public function processPayment(Invoice $invoice, Request $request){

    	Stripe::setApiKey(env('STRIPE_SECRET'));

    	Charge::create ([
		    "amount"      => intval($invoice->price * 100),
		    "currency"    => "usd",
		    "source"      => $request->stripeToken,
		    "description" => "Making test payment.",
	    ]);

    	$payment = Payment::create([
		    "price"         => $invoice->price,
		    "currency"      => "usd",
		    "source"        => $request->stripeToken,
		    "desc"          => "Making test payment.",
	    ]);

    	$order = Order::create([
    		'invoice_id'    => $invoice->id,
		    'payment_id'    => $payment->id,
		    'client_id'     => ($invoice->message->inbox->seller_id != $invoice->message->from_id) ? ($invoice->message->inbox->seller_id) : ($invoice->message->inbox->client_id),
		    'seller_id'     => $invoice->message->from_id
	    ]);

    	$invoice->update([
    		'status' => 'paid'
	    ]);

	    Session::flash('success', 'Payment has been successfully processed.');

	    return redirect()->route('order.show', ['order' => $order->id]);
    }

    public function thankyou(){
    	return view('app.pages.invoice.thankyou');
    }

    public function cancel(Invoice $invoice){
	    $invoice->update([
		    'status' => 'cancelled'
	    ]);
	    return redirect()->back();
    }

    public function reject(Invoice $invoice){
	    $invoice->update([
		    'status' => 'rejected'
	    ]);
	    return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
