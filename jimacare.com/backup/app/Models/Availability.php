<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['user_id', 'type_id', 'available', 'charges'];

    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function type(){
    	return $this->belongsTo(Type::class);
    }
}
