<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'client_id',
        'carer_id',
        'status',
        'message',
        'invited_at',
        'responded_at',
        'rejection_reason',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    /**
     * Get the job/contract this invitation is for
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * Get the client who sent the invitation
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the carer who was invited
     */
    public function carer()
    {
        return $this->belongsTo(User::class, 'carer_id');
    }

    /**
     * Scope for pending invitations
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for accepted invitations
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Scope for rejected invitations
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}

