<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimesheetPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'carer_id',
        'contract_id',
        'period_type',
        'period_start',
        'period_end',
        'total_hours',
        'hourly_rate',
        'subtotal',
        'platform_fee',
        'total_amount',
        'stripe_payment_link_id',
        'stripe_payment_link_url',
        'status',
        'link_sent_at',
        'paid_at',
        'stripe_payment_intent_id',
        'timesheet_ids',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'total_hours' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'link_sent_at' => 'datetime',
        'paid_at' => 'datetime',
        'timesheet_ids' => 'array',
    ];

    /**
     * Get the client
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the carer/childminder/housekeeper
     */
    public function carer()
    {
        return $this->belongsTo(User::class, 'carer_id');
    }

    /**
     * Get the contract/job
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * Get related timesheets
     */
    public function timesheets()
    {
        return Timesheet::whereIn('id', $this->timesheet_ids ?? []);
    }
}

