<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
	    'user_id', 'role_id', 'title', 'company',
	    'start_type', 'start_date', 'end_type', 'end_date',
	    'start_time', 'end_time', 'desc', 'drive',
	    'hourly_rate', 'daily_rate', 'weekly_rate',
	    'how_often', 'beds', 'baths', 'rooms', 'cleaning_type','address','radius','lat','long',
	    'filled_at', 'filled_by_application_id', 'reposted_at'
    ];

    //protected $with = [ 'hourlies', 'nightlies' ];

	protected $casts = [
		'start_date'    => 'datetime:Y-m-d',
		'end_date'      => 'datetime:Y-m-d',
		'start_time'    => 'datetime:H:i',
		'end_time'      => 'datetime:H:i',
		'filled_at'     => 'datetime',
		'reposted_at'   => 'datetime',
	];

	/*public function getRouteKeyName(){
		return 'slug';
	}*/

	public function sluggable(){
		return [
			'slug' => [
				'source' => 'title'
			]
		];
	}

	public function user(){
		return $this->belongsTo(User::class);
	}

	public function role(){
		return $this->belongsTo(Role::class);
	}

	public function types(){
		return $this->belongsToMany(Type::class);
	}

	public function days(){
		return $this->belongsToMany(Day::class);
	}

    public function languages(){
		return $this->belongsToMany(Language::class);
	}

	public function experiences(){
		return $this->belongsToMany(Experience::class);
	}

	public function educations(){
		return $this->belongsToMany(Education::class);
	}

	public function skills(){
		return $this->belongsToMany(Skill::class);
	}

	public function interests(){
		return $this->belongsToMany(Interest::class);
	}

	public function time_availables(){
		return $this->morphMany(TimeAvailable::class, 'typeable');
	}

	public function applications(){
		return $this->HasMany(Application::class);
	}

	/**
	 * Get job applications (new system)
	 */
	public function jobApplications()
	{
		return $this->hasMany(JobApplication::class, 'contract_id');
	}

	/**
	 * Get the selected application (if job is filled)
	 */
	public function selectedApplication()
	{
		return $this->belongsTo(JobApplication::class, 'filled_by_application_id');
	}

	/**
	 * Check if job is filled
	 */
	public function isFilled()
	{
		return !is_null($this->filled_at);
	}

	/**
	 * Get platform commission percentage
	 * Platform takes 33.3333% (1/3), providers get 66.6% (2/3)
	 */
	public function getPlatformFeePercentage()
	{
		// Platform fee is 33.3333% (1/3 of the price)
		return 33.3333;
	}

	/**
	 * Get minimum hourly rate for service providers based on job role
	 * 
	 * @return float Minimum hourly rate
	 */
	public function getMinimumProviderRate()
	{
		// Role IDs: 3 = Carer, 4 = Childminder, 5 = Housekeeper
		$roleId = $this->role_id;
		
		// Carers and Childminders: £20/hour minimum
		if ($roleId == 3 || $roleId == 4) {
			return 20.00;
		}
		
		// Housekeepers: £15/hour minimum
		if ($roleId == 5) {
			return 15.00;
		}
		
		// Default minimum (for other roles or if role not set)
		return 15.00;
	}

	/**
	 * Calculate provider rate (what service provider receives)
	 * Service providers get 66.6% (2/3) of the client's posted price
	 * Enforces minimum rates based on job role
	 * 
	 * @param float $clientRate The rate the client posted
	 * @param string $rateType The type of rate: 'hourly', 'daily', or 'weekly'
	 * @return float The rate the provider will receive (enforced minimum)
	 */
	public function calculateProviderRate($clientRate, $rateType = 'hourly')
	{
		if (!$clientRate || $clientRate <= 0) {
			return 0;
		}

		// Service providers get 66.6% (2/3) of the client's price
		$providerRate = ($clientRate * 66.6667) / 100;

		// Apply minimum rate enforcement for hourly rates only
		if ($rateType === 'hourly') {
			$minimumRate = $this->getMinimumProviderRate();
			if ($providerRate < $minimumRate) {
				$providerRate = $minimumRate;
			}
		}
		// For daily/weekly rates, convert to hourly equivalent to check minimum
		elseif ($rateType === 'daily') {
			// Assume 8 hours per day for minimum calculation
			$hourlyEquivalent = $providerRate / 8;
			$minimumRate = $this->getMinimumProviderRate();
			if ($hourlyEquivalent < $minimumRate) {
				$providerRate = $minimumRate * 8; // Ensure daily rate meets hourly minimum
			}
		}
		elseif ($rateType === 'weekly') {
			// Assume 40 hours per week for minimum calculation
			$hourlyEquivalent = $providerRate / 40;
			$minimumRate = $this->getMinimumProviderRate();
			if ($hourlyEquivalent < $minimumRate) {
				$providerRate = $minimumRate * 40; // Ensure weekly rate meets hourly minimum
			}
		}

		return round($providerRate, 2);
	}

	/**
	 * Get hourly rate for provider (after commission and minimum enforcement)
	 */
	public function getProviderHourlyRate()
	{
		return $this->calculateProviderRate($this->hourly_rate ?? 0, 'hourly');
	}

	/**
	 * Get daily rate for provider (after commission and minimum enforcement)
	 */
	public function getProviderDailyRate()
	{
		return $this->calculateProviderRate($this->daily_rate ?? 0, 'daily');
	}

	/**
	 * Get weekly rate for provider (after commission and minimum enforcement)
	 */
	public function getProviderWeeklyRate()
	{
		return $this->calculateProviderRate($this->weekly_rate ?? 0, 'weekly');
	}

	/**
	 * Calculate platform fee amount
	 * Platform takes 33.3333% (1/3) of client rate, but adjusts if minimum rate is enforced
	 * 
	 * @param float $clientRate The rate the client posted
	 * @param string $rateType The type of rate: 'hourly', 'daily', or 'weekly'
	 * @return float The platform fee amount
	 */
	public function calculatePlatformFee($clientRate, $rateType = 'hourly')
	{
		if (!$clientRate || $clientRate <= 0) {
			return 0;
		}

		// Calculate what provider would get at 66.6%
		$calculatedProviderRate = ($clientRate * 66.6667) / 100;
		
		// Get the actual provider rate (after minimum enforcement)
		$actualProviderRate = $this->calculateProviderRate($clientRate, $rateType);
		
		// Platform fee is the difference between client rate and what provider actually receives
		$platformFee = $clientRate - $actualProviderRate;

		return round($platformFee, 2);
	}

	/**
	 * Get platform fee for hourly rate
	 */
	public function getPlatformFeeHourly()
	{
		return $this->calculatePlatformFee($this->hourly_rate ?? 0, 'hourly');
	}

	/**
	 * Get platform fee for daily rate
	 */
	public function getPlatformFeeDaily()
	{
		return $this->calculatePlatformFee($this->daily_rate ?? 0, 'daily');
	}

	/**
	 * Get platform fee for weekly rate
	 */
	public function getPlatformFeeWeekly()
	{
		return $this->calculatePlatformFee($this->weekly_rate ?? 0, 'weekly');
	}

	/**
	 * Get pricing breakdown for admin view
	 * Returns both client rate, provider rate, and platform fee
	 * 
	 * @return array ['client_rate' => float, 'provider_rate' => float, 'platform_fee' => float, 'type' => string]
	 */
	public function getPricingBreakdown()
	{
		$clientRate = 0;
		$type = 'hourly';

		if ($this->hourly_rate) {
			$clientRate = $this->hourly_rate;
			$type = 'hourly';
		} elseif ($this->daily_rate) {
			$clientRate = $this->daily_rate;
			$type = 'daily';
		} elseif ($this->weekly_rate) {
			$clientRate = $this->weekly_rate;
			$type = 'weekly';
		}

		$providerRate = $this->calculateProviderRate($clientRate, $type);
		$platformFee = $this->calculatePlatformFee($clientRate, $type);

		return [
			'client_rate' => $clientRate,
			'provider_rate' => $providerRate,
			'platform_fee' => $platformFee,
			'type' => $type,
		];
	}

	/**
	 * Get the appropriate rate based on what's set (hourly, daily, or weekly)
	 * Returns provider rate if viewing as service provider, client rate if viewing as client/admin
	 * 
	 * @param bool $forProvider If true, returns provider rate (66.6%), otherwise client rate
	 * @return array ['rate' => float, 'type' => string, 'formatted' => string]
	 */
	public function getDisplayRate($forProvider = false)
	{
		$rate = 0;
		$type = 'hourly';
		$formatted = 'N/A';

		if ($this->hourly_rate) {
			$rate = $forProvider ? $this->getProviderHourlyRate() : $this->hourly_rate;
			$type = 'hourly';
			$formatted = '£' . number_format($rate, 2) . '/hr';
		} elseif ($this->daily_rate) {
			$rate = $forProvider ? $this->getProviderDailyRate() : $this->daily_rate;
			$type = 'daily';
			$formatted = '£' . number_format($rate, 2) . '/day';
		} elseif ($this->weekly_rate) {
			$rate = $forProvider ? $this->getProviderWeeklyRate() : $this->weekly_rate;
			$type = 'weekly';
			$formatted = '£' . number_format($rate, 2) . '/week';
		}

		return [
			'rate' => $rate,
			'type' => $type,
			'formatted' => $formatted,
		];
	}
}
