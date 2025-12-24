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
	    'how_often', 'beds', 'baths', 'rooms', 'cleaning_type'

    ];

    //protected $with = [ 'hourlies', 'nightlies' ];

	protected $casts = [
		'start_date'    => 'datetime:Y-m-d',
		'end_date'      => 'datetime:Y-m-d',
		'start_time'    => 'datetime:H:i',
		'end_time'      => 'datetime:H:i',
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
}
