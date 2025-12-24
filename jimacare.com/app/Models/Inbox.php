<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inbox extends Model
{
    use HasFactory;
    
    //  protected $table = 'inboxes';
    
    protected $fillable = ['client_id', 'seller_id'];

    public function client(){
    	return $this->belongsTo(User::class, 'client_id');
    }

	public function seller(){
		return $this->belongsTo(User::class, 'seller_id');
	}

	public function messages(){
		return $this->hasMany(Message::class);
	}
}
