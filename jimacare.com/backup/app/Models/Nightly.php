<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nightly extends Model
{
    use HasFactory;
    public $timestamps = false;

	public function day(){
		return $this->belongsTo(Day::class);
	}

	public function contract(){
		return $this->belongsTo(Contract::class);
	}
}
