<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [ 'user_id', 'role_id', 'title', 'slug'];

	public function sluggable() :array {
		return [
			'slug' => [
				'source' => 'title'
			]
		];
	}

    public function role(){
    	return $this->belongsTo(Role::class);
    }
}
