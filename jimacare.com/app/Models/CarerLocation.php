<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarerLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'carer_id',
        'latitude',
        'longitude',
        'is_active',
        'last_updated',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
        'last_updated' => 'datetime',
    ];

    /**
     * Get the carer
     */
    public function carer()
    {
        return $this->belongsTo(User::class, 'carer_id');
    }
}

