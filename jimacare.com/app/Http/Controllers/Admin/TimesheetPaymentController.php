<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimesheetPayment;
use App\Services\TimesheetPaymentService;
use App\Services\StripePaymentLinkService;
use Illuminate\Http\Request;

class TimesheetPaymentController extends Controller
{
    protected $paymentService;
    protected $stripeService;

    public function __construct(
        TimesheetPaymentService $paymentService,
        StripePaymentLinkService $stripeService
    ) {
        $this->paymentService = $paymentService;
        $this->stripeService = $stripeService;
        $this->middleware('auth');
    }

    /**
     * Display all timesheet payments
     */
    public function index(Request $request)
    {
        $query = TimesheetPayment::with(['carer', 'client', 'contract'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('client_id') && $request->client_id) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->has('carer_id') && $request->carer_id) {
            $query->where('carer_id', $request->carer_id);
        }

        $payments = $query->paginate(25);

        // Statistics
        $totalPayments = TimesheetPayment::count();
        $pendingPayments = TimesheetPayment::where('status', 'pending')->count();
        $paidPayments = TimesheetPayment::where('status', 'paid')->count();
        $totalRevenue = TimesheetPayment::where('status', 'paid')->sum('total_amount');
        $totalPlatformFees = TimesheetPayment::where('status', 'paid')->sum('platform_fee');

        // Get clients and carers for filters
        $clients = \App\Models\User::whereHas('role', function($q) {
            $q->where('slug', 'client');
        })->orderBy('firstname')->get();

        $carers = \App\Models\User::whereHas('role', function($q) {
            $q->whereIn('slug', ['carer', 'childminder', 'housekeeper']);
        })->orderBy('firstname')->get();

        return view('admin.pages.timesheet-payments.index', compact(
            'payments',
            'totalPayments',
            'pendingPayments',
            'paidPayments',
            'totalRevenue',
            'totalPlatformFees',
            'clients',
            'carers'
        ));
    }

    /**
     * View single payment details
     */
    public function show(TimesheetPayment $payment)
    {
        $payment->load(['carer', 'client', 'contract']);
        $timesheets = $payment->timesheets()->get();

        return view('admin.pages.timesheet-payments.show', compact('payment', 'timesheets'));
    }

    /**
     * Generate and send payment links (admin)
     */
    public function generateAndSendPayments(Request $request)
    {
        $request->validate([
            'period_type' => 'required|in:weekly,monthly',
            'client_id' => 'required|exists:users,id',
            'carer_id' => 'nullable|exists:users,id',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after:period_start',
        ]);

        $result = app(\App\Http\Controllers\TimesheetPaymentController::class)
            ->generateAndSendPayments($request);

        return $result;
    }
}

