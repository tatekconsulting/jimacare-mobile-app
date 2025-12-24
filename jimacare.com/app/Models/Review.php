<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['seller_id', 'client_id', 'order_id', 'stars',  'desc'];

    public function seller(){
    	return $this->belongsTo(User::class);
    }

    public function client(){
	    return $this->belongsTo(User::class, 'client_id');
    }

    public function order(){
    	return $this->belongsTo(Order::class);
    }
}
