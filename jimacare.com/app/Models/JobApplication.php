<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'carer_id',
        'invitation_id',
        'cover_letter',
        'proposed_rate',
        'status',
        'response_type',
        'responded_at',
        'rejection_reason',
    ];

    protected $casts = [
        'proposed_rate' => 'decimal:2',
        'responded_at' => 'datetime',
    ];

    /**
     * Get the job/contract this application is for
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * Get the carer who applied
     */
    public function carer()
    {
        return $this->belongsTo(User::class, 'carer_id');
    }

    /**
     * Get the invitation (if this application came from an invitation)
     */
    public function invitation()
    {
        return $this->belongsTo(JobInvitation::class);
    }

    /**
     * Scope for pending applications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for accepted applications
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }
}

