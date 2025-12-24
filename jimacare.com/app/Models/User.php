<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Timesheet;
use App\Models\JobApplication;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'profile', 'profile_locked', 'profile_verified_at', 'profile_verification_id', 'firstname', 'lastname', 'email', 'phone', 'gender', 'dob',
        'country', 'state', 'city', 'address', 'postcode', 'lat', 'long',

        'referee1_name', 'referee1_email', 'referee1_phone', 'referee1_country_id',
		'referee1_child_age', 'referee1_how_long', 'referee1_how_contact','referee1_status',

		'referee2_name', 'referee2_email', 'referee2_phone', 'referee2_country_id',
		'referee2_child_age', 'referee2_how_long', 'referee2_how_contact','referee2_status',

        'years_experience', 'fee', 'service_charges', 'info', 'other',
        'dbs', 'dbs_type', 'dbs_issue', 'dbs_cert',
        'role_id', 'password','power_admin', 'must_change_password',

        'approved', 'insured', 'vaccinated','status','last_login','verified_video',

		'has_care_training','has_ni_number','ni_number'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'timestamp'         => 'date',
        'last_login'         => 'datetime',
    ];

    public function role(){
    	return $this->belongsTo(Role::class);
    }

	public function refrences(){
		return $this->hasMany(Reference::class);
	}
	public function reference1(){
		return $this->refrences()->where('type',1);
	}
	public function reference2(){
		return $this->refrences()->where('type',2);
	}

    public function country(){
    	return $this->belongsTo(Country::class);
    }

	public function languages(){
		return $this->belongsToMany(Language::class);
	}

	public function types(){
    	return $this->belongsToMany(Type::class);
    }

	public function interests(){
		return $this->belongsToMany(Interest::class);
	}

	public function experiences(){
		return $this->belongsToMany(Experience::class);
	}

	public function educations(){
    	return $this->belongsToMany(Education::class, 'education_user');
	}

	public function skills(){
		return $this->belongsToMany(Skill::class);
	}

	public function availabilities(){
    	return $this->hasMany(Availability::class);
	}

	public function time_availables(){
		return $this->morphMany(TimeAvailable::class, 'typeable');
	}

	public function days(){
    	return $this->belongsToMany(Day::class);
	}

	public function reviews(){
    	return $this->hasMany(Review::class, 'seller_id');
	}

	public function getReviewsAvgAttribute(){
    	return $this->reviews()->average('stars');
	}

	public function getReviewsCountAttribute(){
		return $this->reviews()->count();
	}

	public function documents(){
    	return $this->hasMany(Document::class);
	}

	// Timesheets relationship (as carer)
	public function timesheets(){
		return $this->hasMany(Timesheet::class, 'carer_id');
	}

	// Job applications relationship (as carer)
	public function jobApplications(){
		return $this->hasMany(JobApplication::class, 'carer_id');
	}

	public function getNameAttribute(){
		return ($this->firstname ?? '') . ' ' . ($this->lastname[0] ?? '');
	}

	public function getRoleSlugAttribute(){
		return $this->role->slug;
	}

	public function getAgeAttribute(){
		return Carbon::parse($this->dob)->age;
	}

	public function getPostalAttribute(){
    	return trim( substr($this->postcode ?? '', 0, -3));
	}

}
