<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['payment_id', 'invoice_id', 'seller_id', 'client_id', 'desc', 'price', 'status'];

    public function invoice(){
    	return $this->belongsTo(Invoice::class);
    }

    public function payment(){
    	return $this->belongsTo(Payment::class);
    }

    public function seller(){
	    return $this->belongsTo(User::class);
    }

	public function client(){
		return $this->belongsTo(User::class);
	}

	public function review(){
    	return $this->hasOne(Review::class);
	}
}
