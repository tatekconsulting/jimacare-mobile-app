<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'expiration', 'expiry_date', 'path', 'compliance_status'];

	protected $casts = [
		'expiration'         => 'date',
		'expiry_date'        => 'date',
	];


	/**
	 * Check if document is expired
	 */
	public function isExpired()
	{
		if (!$this->expiration) {
			return false;
		}
		return $this->expiration->isPast();
	}

	/**
	 * Check if document is expiring soon (within 30 days)
	 */
	public function isExpiringSoon()
	{
		if (!$this->expiration) {
			return false;
		}
		return $this->expiration->isFuture() && $this->expiration->diffInDays(now()) <= 30;
	}

	/**
	 * Update compliance status based on expiration date
	 */
	public function updateComplianceStatus()
	{
		if (!$this->expiration) {
			$this->compliance_status = null;
			return;
		}

		if ($this->isExpired()) {
			$this->compliance_status = 'expired';
		} elseif ($this->isExpiringSoon()) {
			$this->compliance_status = 'expiring';
		} else {
			$this->compliance_status = 'valid';
		}
	}

	/**
	 * Boot method to auto-update compliance status
	 */
	protected static function boot()
	{
		parent::boot();

		static::saving(function ($document) {
			$document->updateComplianceStatus();
		});
	}

    public function user(){
    	return $this->belongsTo(User::class);
    }
}
