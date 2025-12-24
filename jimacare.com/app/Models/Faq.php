<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['role_id', 'title', 'desc'];

	public function role(){
		return $this->belongsTo(Role::class);
	}
}
