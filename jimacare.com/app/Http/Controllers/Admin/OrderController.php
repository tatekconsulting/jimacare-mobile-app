<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\TimesheetPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		// Get TimesheetPayments (current active payment system)
		$paymentsQuery = TimesheetPayment::with(['carer', 'client', 'contract'])
			->orderBy('created_at', 'desc');

		// Apply filters
		if ($request->has('status') && $request->status) {
			$paymentsQuery->where('status', $request->status);
		}

		if ($request->has('client_id') && $request->client_id) {
			$paymentsQuery->where('client_id', $request->client_id);
		}

		if ($request->has('carer_id') && $request->carer_id) {
			$paymentsQuery->where('carer_id', $request->carer_id);
		}

		if ($request->has('date_from') && $request->date_from) {
			$paymentsQuery->whereDate('created_at', '>=', $request->date_from);
		}

		if ($request->has('date_to') && $request->date_to) {
			$paymentsQuery->whereDate('created_at', '<=', $request->date_to);
		}

		// Search
		if ($request->has('search') && $request->search) {
			$search = $request->search;
			$paymentsQuery->where(function($q) use ($search) {
				$q->whereHas('client', function($query) use ($search) {
					$query->where('firstname', 'like', "%{$search}%")
						->orWhere('lastname', 'like', "%{$search}%")
						->orWhere('email', 'like', "%{$search}%");
				})->orWhereHas('carer', function($query) use ($search) {
					$query->where('firstname', 'like', "%{$search}%")
						->orWhere('lastname', 'like', "%{$search}%")
						->orWhere('email', 'like', "%{$search}%");
				});
			});
		}

		$payments = $paymentsQuery->paginate(25)->withQueryString();

		// Statistics
		$stats = [
			'total_payments' => TimesheetPayment::count(),
			'pending_payments' => TimesheetPayment::where('status', 'pending')->count(),
			'link_sent_payments' => TimesheetPayment::where('status', 'link_sent')->count(),
			'paid_payments' => TimesheetPayment::where('status', 'paid')->count(),
			'failed_payments' => TimesheetPayment::where('status', 'failed')->count(),
			'total_revenue' => TimesheetPayment::where('status', 'paid')->sum('total_amount'),
			'total_platform_fees' => TimesheetPayment::where('status', 'paid')->sum('platform_fee'),
			'total_pending_amount' => TimesheetPayment::where('status', 'pending')->sum('total_amount'),
		];

		// Get clients and carers for filters
		$clients = \App\Models\User::whereHas('role', function($q) {
			$q->where('slug', 'client');
		})->orderBy('firstname')->get();

		$carers = \App\Models\User::whereHas('role', function($q) {
			$q->whereIn('slug', ['carer', 'childminder', 'housekeeper']);
		})->orderBy('firstname')->get();

		// Legacy orders (for backward compatibility)
		$legacyOrders = Order::with(['invoice', 'client', 'seller', 'payment'])
			->orderBy('created_at', 'desc')
			->limit(10)
			->get();

		return view('admin.pages.order.index', compact('payments', 'stats', 'clients', 'carers', 'legacyOrders'));
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
		//
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

	public function exportOrders(Request $request)
	{
		$fileName = 'Timesheet-Payments-' . date('Y-m-d') . '.csv';
		
		// Get payments based on filters (same as index)
		$paymentsQuery = TimesheetPayment::with(['carer', 'client', 'contract'])
			->orderBy('created_at', 'desc');

		// Apply same filters as index
		if ($request->has('status') && $request->status) {
			$paymentsQuery->where('status', $request->status);
		}
		if ($request->has('client_id') && $request->client_id) {
			$paymentsQuery->where('client_id', $request->client_id);
		}
		if ($request->has('carer_id') && $request->carer_id) {
			$paymentsQuery->where('carer_id', $request->carer_id);
		}
		if ($request->has('date_from') && $request->date_from) {
			$paymentsQuery->whereDate('created_at', '>=', $request->date_from);
		}
		if ($request->has('date_to') && $request->date_to) {
			$paymentsQuery->whereDate('created_at', '<=', $request->date_to);
		}

		$payments = $paymentsQuery->get();

		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=$fileName",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0"
		);

		$columns = array(
			'Payment ID',
			'Client Name',
			'Client Email',
			'Service Provider Name',
			'Service Provider Email',
			'Service Provider Role',
			'Period Type',
			'Period Start',
			'Period End',
			'Total Hours',
			'Hourly Rate',
			'Subtotal',
			'Platform Fee',
			'Total Amount',
			'Status',
			'Created Date',
			'Paid Date'
		);

		$callback = function () use ($payments, $columns) {
			$file = fopen('php://output', 'w');
			fputcsv($file, $columns);

			foreach ($payments as $payment) {
				$row = [
					'Payment ID' => $payment->id,
					'Client Name' => ($payment->client ? $payment->client->firstname . ' ' . $payment->client->lastname : 'N/A'),
					'Client Email' => $payment->client->email ?? 'N/A',
					'Service Provider Name' => ($payment->carer ? $payment->carer->firstname . ' ' . $payment->carer->lastname : 'N/A'),
					'Service Provider Email' => $payment->carer->email ?? 'N/A',
					'Service Provider Role' => $payment->carer->role->title ?? 'N/A',
					'Period Type' => ucfirst($payment->period_type),
					'Period Start' => $payment->period_start ? $payment->period_start->format('d M Y') : 'N/A',
					'Period End' => $payment->period_end ? $payment->period_end->format('d M Y') : 'N/A',
					'Total Hours' => number_format($payment->total_hours, 2),
					'Hourly Rate' => '£' . number_format($payment->hourly_rate, 2),
					'Subtotal' => '£' . number_format($payment->subtotal, 2),
					'Platform Fee' => '£' . number_format($payment->platform_fee, 2),
					'Total Amount' => '£' . number_format($payment->total_amount, 2),
					'Status' => ucfirst($payment->status),
					'Created Date' => $payment->created_at ? $payment->created_at->format('d M Y H:i:s') : 'N/A',
					'Paid Date' => $payment->paid_at ? $payment->paid_at->format('d M Y H:i:s') : 'N/A',
				];
				fputcsv($file, $row);
			}

			fclose($file);
		};

		return response()->stream($callback, 200, $headers);
	}
}
