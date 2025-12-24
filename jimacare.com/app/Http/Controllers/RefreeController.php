<?php

namespace App\Http\Controllers;

use App\Mail\RefereeUpdate;
use App\Mail\UserReference;
use App\Models\Reference;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RefreeController extends Controller
{
	public function cancelReference($id, $reference)
	{
		$user = User::find($id);
		$user->update(
			[
				'referee' . $reference . '_status' => false
			]
		);

		return redirect('home')->with(['notice' => "Reference Cancelled Successfully!"]);

	}
	public function confirmReference($id, $reference)
	{
		$user = User::find($id);
		return view('app.pages.reference.edit')->with('user', $user)->with('reference', $reference);
	}
	public function updateReference(Request $request)
	{
		$user = User::find($request->user_id);
		$user->update(
			[
				'referee' . $request->type . '_status' => true
			]
		);
		$values = [
			'first_name' => $request->input('first_name'),
			'last_name' => $request->input('last_name'),
			'email' => $request->input('email'),
			'job_title' => $request->input('job_title'),
			'organisation' => $request->input('organisation'),
			'responsibility' => $request->input('responsibility'),
			'from' => $request->input('from'),
			'to' => $request->input('to'),
			'emp_job_title' => $request->input('emp_job_title'),
			'emp_currently_work' => $request->emp_currently_work == "on" ? true : false,
			'emp_key_duty' => $request->input('emp_key_duty'),
			'emp_safety_issue' => $request->emp_safety_issue == "true" ? true : false,
			'comment' => $request->input('comment'),
			'emp_again' => $request->emp_again == "true" ? true : false,
		];

		$reference = Reference::updateOrCreate([
			'user_id' => $user->id,
			'type' => $request->type,
		], $values);
		Mail::to($user)->send(new UserReference($user,$reference));

		$admins = User::where(['role_id' => 1, 'status' => 'active'])->get();
		foreach ($admins as $admin) {
			Mail::to($admin)->send(new RefereeUpdate($user,$reference));
		}



		return redirect('home')->with(['notice' => "Reference Approved Successfully!"]);
	}
}
