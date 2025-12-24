<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['title', 'slug', 'role_id'];

	public function role(){
		return $this->belongsTo(Role::class);
	}

	public function users(){
		return $this->belongsToMany(User::class);
	}
}
