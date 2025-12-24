<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    use HasFactory;

	protected $fillable = [
       'user_id',
       'type',
       'first_name',
       'last_name',
       'email',
       'job_title',
       'organisation',
       'from',
       'to',
       'emp_job_title',
       'emp_currently_work',
       'emp_key_duty',
       'emp_safety_issue',
       'comment',
       'emp_again',
	   'responsibility',
    ];

	public function user(){
		return $this->belongsTo(User::class);
	}
}
