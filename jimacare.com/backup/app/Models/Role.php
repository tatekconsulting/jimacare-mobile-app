<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function users(){
    	return $this->hasMany(User::class);
    }

	public function experiences(){
		return $this->hasMany(Experience::class);
	}

	public function skills(){
		return $this->hasMany(Skill::class);
	}

	public function interests(){
		return $this->hasMany(Interest::class);
	}

	public function posts(){
    	return $this->hasMany(Post::class);
	}

	public function faqs(){
    	return $this->hasMany(Faq::class);
	}

	public function types(){
    	return $this->hasMany(Type::class);
	}
}
