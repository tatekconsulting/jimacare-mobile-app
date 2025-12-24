<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [ 'message_id', 'price', 'status', 'from_id', 'to_id' ];

	public function from(){
		return $this->belongsTo(User::class);
	}

	public function to(){
		return $this->belongsTo(User::class);
	}

	public function message(){
		return $this->belongsTo(Message::class);
	}

	public function order(){
		return $this->hasOne(Order::class);
	}

}
