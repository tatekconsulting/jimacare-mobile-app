<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'carer_id',
        'client_id',
        'job_application_id',
        'date',
        'work_date',
        'clock_in',
        'clock_in_verified',
        'clock_in_verification_photo',
        'clock_in_verification_confidence',
        'clock_in_verified_at',
        'clock_out',
        'clock_out_verified',
        'clock_out_verification_photo',
        'clock_out_verification_confidence',
        'clock_out_verified_at',
        'hours_worked',
        'hourly_rate',
        'total_amount',
        'notes',
        'status',
        'approved_at',
        'dispute_reason',
        'cancelled_at',
        'cancellation_reason',
        'cancelled_by',
        'paid_at',
        'location_lat',
        'location_lng',
    ];

    protected $casts = [
        'date' => 'date',
        'work_date' => 'date',
        'clock_in' => 'datetime',
        'clock_in_verified' => 'boolean',
        'clock_in_verification_confidence' => 'decimal:2',
        'clock_in_verified_at' => 'datetime',
        'clock_out' => 'datetime',
        'clock_out_verified' => 'boolean',
        'clock_out_verification_confidence' => 'decimal:2',
        'clock_out_verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'paid_at' => 'datetime',
        'hours_worked' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'location_lat' => 'decimal:8',
        'location_lng' => 'decimal:8',
    ];

    /**
     * Get the job/contract
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * Get the carer
     */
    public function carer()
    {
        return $this->belongsTo(User::class, 'carer_id');
    }

    /**
     * Get the client
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the user who cancelled the timesheet
     */
    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Scope for pending timesheets
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved timesheets
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for cancelled timesheets
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}

