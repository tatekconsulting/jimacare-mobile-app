<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes, Sluggable;

    protected $fillable = ['user_id', 'role_id', 'title', 'slug', 'image', 'banner', 'desc'];

	/*public function getRouteKeyName(){
		return 'slug';
	}*/

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
