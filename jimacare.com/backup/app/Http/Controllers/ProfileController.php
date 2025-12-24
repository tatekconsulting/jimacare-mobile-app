<?php

namespace App\Http\Controllers;

use App\Mail\ProfileCompletion;
use App\Models\Availability;
use App\Models\Country;
use App\Models\Day;
use App\Models\Document;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Interest;
use App\Models\Skill;
use App\Models\TimeAvailable;
use App\Models\TimeType;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware(['auth', 'verified']);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$profile = auth()->user();
		$countries = Country::all();
		$types = Type::where('role_id', $profile->role_id)->get();
		$skills = Skill::where('role_id', $profile->role_id)->get();
		$interests = Interest::where('role_id', $profile->role_id)->get();
		$experiences = Experience::where('role_id', $profile->role_id)->get();
		$educations = Education::where('role_id', $profile->role_id)->get();
		$days = Day::all();
		$time_types = TimeType::all();
		return view('app.pages.profile.index_new', compact('profile', 'countries', 'types', 'skills', 'interests', 'experiences', 'educations', 'days', 'time_types'));
	}

	public function store(Request $request)
	{
		$profile = auth()->user();
		$type = $request->type;
		if (!$type) {
			return redirect('/');
		} elseif ($type == 'basic') {
			$validation = [
				'firstname' => 'bail|required|string|max:255',
				'lastname' => 'bail|required|string|max:255',
				'email' => 'bail|required|string|min:4|max:255|unique:users,email,' . $profile->id,
				'phone' => 'bail|required|string|min:4|max:255|unique:users,phone,' . $profile->id,
				'gender' => 'bail|required|string|in:male,female',
				'address' => 'bail|required|string|min:4',
				'city' => 'bail|required|string|min:4|max:255',
				//'state'     => 'bail|required|string|min:4|max:255',
				'country' => 'bail|required|string|min:4|max:255',
				'postcode' => 'bail|required|string',
				'lat' => 'bail|required|numeric',
				'long' => 'bail|required|numeric',
			];
			if ($profile->role_id !== 2){
				$validation['dob']= 'bail|required|date';
			}
		} elseif ($type == 'pricing') {
			$validation = [
				'language' => 'bail|required|array',
				'language.*' => 'bail|required|numeric|exists:languages,id',
			];
			if ($profile->role_id == 3) {
				$validation = array_merge($validation, [
					'day' => 'bail|required|array',
					'day.*' => 'bail|required|numeric|exists:days,id',

					'availability' => 'bail|required|array',
					'availability.*' => 'bail|required|array',
					'availability.*.available' => 'bail|sometimes|bool',
					'availability.*.charges' => 'bail|sometimes|nullable|numeric|min:1',
				]);
			}
			if ($profile->role_id == 4) {
				$validation = array_merge($validation, [
					'availability' => 'bail|required|array',
					'availability.*' => 'bail|required|array',
					'availability.*.*' => 'bail|required|numeric|exists:time_types,id',
					'fee' => 'bail|required|numeric|min:1',
					'service_charges' => 'bail|required|numeric|min:0',
				]);
			}
		} elseif ($type == 'experiences') {
			if ($profile->role_id > 2) {
				$validation = [
					'years_experience' => 'bail|required|numeric',

					//Experience Validation
					'experience' => 'bail|required|array',
					'experience.*' => 'bail|required|numeric|exists:experiences,id',

					//Skills
					'skill' => 'bail|required|array',
					'skill.*' => 'bail|required|numeric|exists:skills,id',

					'info' => 'bail|required|string|min:4',
					'other' => 'bail|required|string|min:4',
				];
			}
			if ($profile->role_id == 4) {
				$validation = array_merge($validation, [
					'interest' => 'bail|required|array',
					'interest.*' => 'bail|required|numeric|exists:interests,id',
					'education' => 'bail|required|array',
					'education.*' => 'bail|required|numeric|exists:education,id',
				]);
			}
		} elseif ($type == 'references') {
			if ($profile->role_id > 2) {
				$validation = [
					//DBS Validation
					'dbs' => 'bail|required|string|in:yes,no',
				];
				if (($request->dbs ?? 'no') == 'yes') {
					$validation = array_merge($validation, [
						'dbs_type' => 'bail|required|string|in:basic,standard,enhanced',
						'dbs_issue' => 'bail|required|date',
						'dbs_cert' => 'bail|required|string|min:4|max:255',
					]);
				}
				$validation = array_merge($validation, [
					'referee1_name' => 'bail|required|string|max:255',
					'referee1_email' => 'bail|required|string|min:4|max:255',
					'referee1_phone' => 'bail|required|string|min:4|max:255',
					'referee1_country_id' => 'bail|required|numeric|exists:countries,id',
					'referee1_how_long' => 'bail|required|string|max:255',
					'referee1_how_contact' => 'bail|required|string|min:4|max:255',

					'referee2_name' => 'bail|required|string|max:255',
					'referee2_email' => 'bail|required|string|min:4|max:255',
					'referee2_phone' => 'bail|required|string|min:4|max:255',
					'referee2_country_id' => 'bail|required|numeric|exists:countries,id',
					'referee2_how_long' => 'bail|required|string|max:255',
					'referee2_how_contact' => 'bail|required|string|min:4|max:255',
				]);
			}
		}
		$request->validate($validation);

		($request->dbs && $request->dbs == 'yes') ? $request->merge(['dbs' => true]) : $request->merge(['dbs' => false]);
		$data = $request->except(['_token']);
		$profile->update($data);

		if ($request->has('day')) {
			$profile->days()->sync($request->day);
		}
		if ($request->has('skill')) {
			$profile->skills()->sync($request->skill);
		}
		if ($request->has('language')) {
			$profile->languages()->sync($request->language);
		}
		if ($request->has('experience')) {
			$profile->experiences()->sync($request->experience);
		}
		if ($request->has('education')) {
			$profile->educations()->sync($request->education);
		}
		if ($request->has('interest')) {
			$profile->interests()->sync($request->interest);
		}
		if ($profile->role_id == 3) {
			if ($request->has('availability')) {
				$profile->availabilities()->update([
					'available' => false
				]);
				foreach ($request->availability as $key => $availability) {
					if ($availability['available'] ?? false) {
						Availability::updateOrCreate([
							'user_id' => $profile->id,
							'type_id' => $key,
						], [
							'available' => $availability['available'],
							'charges' => $availability['charges']
						]);
					}
				}
			}
		}
		if ($profile->role_id == 4) {
			if ($request->has('availability')) {
				$profile->time_availables()->delete();
				foreach ($request->availability as $day => $avails) {
					foreach ($avails as $time) {
						$profile->time_availables()->create([
							'day_id' => $day,
							'type_id' => $time
						]);
					}
				}
			}
		}

		if ($type == 'basic') {
			return redirect('profile?tab=pricing')->with(['notice' => "Profile Updated!"]);
		} elseif ($type == 'pricing') {
			if (in_array($profile->role_id, [1, 2])) {
				if (is_null($profile->profile_completed_at)) {
					$users = User::where(['role_id' => 1, 'status' => 'active'])->get();
					foreach ($users as $user) {
						Mail::to($user)->send(new ProfileCompletion($profile));
					}
					$profile->profile_completed_at = now();
					$profile->save();
				}
				return redirect('profile?tab=pricing')->with(['notice' => "Profile Completed!"]);
			}
			return redirect('profile?tab=experiences')->with(['notice' => "Profile Updated!"]);
		} elseif (!in_array($profile->role_id, [1, 2]) && $type == 'experiences') {
			return redirect('profile?tab=references')->with(['notice' => "Profile Updated!"]);
		} elseif (!in_array($profile->role_id, [1, 2]) && $type == 'references') {
			if (is_null($profile->profile_completed_at)) {
				$users = User::where(['role_id' => 1, 'status' => 'active'])->get();
				foreach ($users as $user) {
					Mail::to($user)->send(new ProfileCompletion($profile));
				}
				$profile->profile_completed_at = now();
				$profile->save();
			}
			return redirect('profile?tab=references')->with(['notice' => "Profile Completed!"]);
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\User  $user
	 * @return \Illuminate\Http\Response
	 */
	public function show(User $user)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\User  $user
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(User $user)
	{
		//
	}

	public function photo(){
		$profile = auth()->user();
		return view('app.pages.profile.photo', compact('profile'));
	}

	public function storePhoto(Request $request){
		if (!$request->hasFile('profile')) {
			return abort(404);
		}
		$profile = auth()->user();
		$path = $request->file('profile')->store('public/profile');
		$path = 'storage/' . (explode('public/', $path)[1]);
		$profile->profile = $path;
		$profile->save();
		return asset($path);
	}

	public function video(){
		$profile = auth()->user();
		return view('app.pages.profile.video', compact('profile'));
	}

	public function storeVideo(Request $request)
	{
		$profile = auth()->user();
		if ($request->action && $request->action == 'remove' && $profile->video) {
			if (Storage::disk('public')->delete(str_replace('storage/', '', auth()->user()->video))) {
				$profile->video = null;
				$profile->save();
				return response()->json(['path' => $profile->video]);
			}
		} elseif (!$request->hasFile('video')) {
			return abort(404);
		}
		$path = $request->file('video')->store('public/video');
		$path = 'storage/' . (explode('public/', $path)[1]);
		$profile->video = $path;
		$profile->save();
		return response()->json(['path' => asset($path)]);
	}

	public function ratings(){
		$profile = auth()->user();
		$reviews = $profile->reviews;
		return view('app.pages.profile.ratings', compact('profile', 'reviews'));
	}

	public function storeRatings(){

	}

	public function documents(){
		$profile = auth()->user();
		$documents = Document::where('user_id', $profile->id)->get();
		return view('app.pages.profile.documents', compact('profile', 'documents'));
	}

	public function storeDocuments(Request $request){
		$profile = auth()->user();

		/*$validation = [
			//'id' => 'bail|required|array',
			'id.*' => 'bail|sometimes|numeric',
			'name' => 'bail|required|array',
			'name.*' => 'bail|sometimes|string|min:4|max:255',
			'expiration' => 'bail|required|array',
			'expiration.*' => 'bail|sometimes|date',
			'file.*' => 'bail|sometimes|file',
		];

		$request->validate($validation);*/
		//return $request->allFiles('doc');

		$ids = [];

		if($request->has('doc')){
			foreach($request->doc as $k => $val) {
				$data = [
					'name'          => $val['name'] ?? '',
					'expiration'    => strtotime($val['expiration'] ?? now() ),
				];

				if($request->hasFile('doc.'. $k . '.file')) {
					$path = $request->file('doc.' . $k . '.file')
						->store('public/document');
					$data['path'] = 'storage/' . (explode('public/', $path)[1]);
					//$request->allFiles('doc');
				}

				//$path = $val->file->store('public/document');

				if($val['id'] ?? false ){
					$ids[] = $val['id'];
					$profile->documents()->where('id', $val['id'])->update($data);
				}else{
					$d = $profile->documents()->create($data);
					$ids[] = $d->id;
				}
			}
		}

		$profile->documents()->whereNull('path')->orWhereNotIn('id', $ids)->delete();

		return redirect()->back();
	}
}
