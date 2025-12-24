<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['from_id', 'message', 'type'];

    public function inbox(){
    	return $this->belongsTo(Inbox::class);
    }

	public function from(){
		return $this->belongsTo(User::class, 'from_id');
	}

	public function invoice(){
		return $this->hasOne(Invoice::class);
	}
}
