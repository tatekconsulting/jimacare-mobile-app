<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeAvailable extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
    	'typeable_id','typeable_type', 'day_id', 'type_id'
    ];

	public function typeable(){
		return $this->morphTo();
	}

	public function type(){
		return $this->belongsTo(TimeType::class);
	}

	public function day(){
		return $this->belongsTo(Day::class);
	}
}
