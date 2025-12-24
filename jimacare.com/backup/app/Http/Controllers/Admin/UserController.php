<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Contract;
use App\Models\Country;
use App\Models\Day;
use App\Models\Document;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Interest;
use App\Models\Role;
use App\Models\Skill;
use App\Models\TimeType;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		$where = [];
		if ($request->type) {
			$where['role_id'] = $request->type;
		}
		if ($request->status) {
			$where['status'] = $request->status;
		}
		$users = User::where($where)->where(function ($query) use ($request) {
			$query->where('firstname', 'like', '%' . $request->name . '%')
				->orWhere('lastname', 'like', '%' . $request->name . '%')
				->orWhere('phone', 'like', '%' . $request->name . '%');
		})->paginate(15);
		$roles = Role::where('slug', '<>', 'admin')->get();
		return view('admin.pages.user.index', compact('users', 'roles'));
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
	{
		$profile = $user;
		$countries = Country::all();
		$types = Type::where('role_id', $profile->role_id)->get();
		$skills = Skill::where('role_id', $profile->role_id)->get();
		$interests = Interest::where('role_id', $profile->role_id)->get();
		$experiences = Experience::where('role_id', $profile->role_id)->get();
		$educations = Education::where('role_id', $profile->role_id)->get();
		$documents = Document::where('user_id', $profile->id)->get();
		$days = Day::all();
		$time_types = TimeType::all();

		return view('admin.pages.user.edit', compact('profile', 'documents', 'countries', 'types', 'skills', 'interests', 'experiences', 'educations', 'days', 'time_types'));
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
    	$profile = $user;

	    $validation = [
			'firstname' => 'bail|required|string|max:255',
			'lastname' => 'bail|required|string|max:255',
			'email' => 'bail|required|string|min:4|max:255|unique:users,email,' . $profile->id,
			'phone' => 'bail|required|string|min:4|max:255|unique:users,phone,' . $profile->id,
			'dob' => 'bail|required|date',
			'gender' => 'bail|required|string|in:male,female',
			'city' => 'bail|required|string|min:4|max:255',
			//'state'     => 'bail|required|string|min:4|max:255',
			'country' => 'bail|required|string',
			'postcode' => 'bail|required|string',
			'address' => 'bail|required|string|min:4',

		    'language'   => 'bail|required|array',
		    'language.*' => 'bail|required|numeric|exists:languages,id',

	    ];

	    if ($profile->role_id > 2) {
		    $validation = array_merge($validation, [
			    'years_experience' => 'bail|required|numeric',
			    'info'             => 'bail|required|string|min:4',
			    'other'            => 'bail|required|string|min:4',

			    //Experience Validation
			    'experience'       => 'bail|required|array',
			    'experience.*'     => 'bail|required|numeric|exists:experiences,id',

			    //Skills
			    'skill'            => 'bail|required|array',
			    'skill.*'          => 'bail|required|numeric|exists:skills,id',

			    //DBS Validation
			    'dbs'              => 'bail|required|string|in:yes,no',
		    ]);
			if (($request->dbs ?? 'no') == 'yes') {
				$validation = array_merge($validation, [
					'dbs_type' => 'bail|required|string|in:basic,standard,enhanced',
					'dbs_issue' => 'bail|required|date',
					'dbs_cert' => 'bail|required|string|min:4|max:255',
				]);
			}
		}

		//Reference Validation
		if (!in_array($profile->role_id, [1, 2])) {
			$validation = array_merge($validation, [
				'referee1_name' => 'bail|required|string|max:255',
				'referee1_email' => 'bail|required|string|min:4|max:255',
				'referee1_phone' => 'bail|required|string|min:4|max:255',
				'referee1_country_id' => 'bail|required|numeric|exists:countries,id',
				'referee1_how_long' => 'bail|required|string|min:4|max:255',
				'referee1_how_contact' => 'bail|required|string|min:4|max:255',

				'referee2_name' => 'bail|required|string|max:255',
				'referee2_email' => 'bail|required|string|min:4|max:255',
				'referee2_phone' => 'bail|required|string|min:4|max:255',
				'referee2_country_id' => 'bail|required|numeric|exists:countries,id',
				'referee2_how_long' => 'bail|required|string|min:4|max:255',
				'referee2_how_contact' => 'bail|required|string|min:4|max:255',
			]);

		    if ($profile->role_id == 4) {
			    $validation = array_merge($validation, [
				    'referee1_child_age' => 'bail|required|string|max:255',
				    'referee2_child_age' => 'bail|required|string|max:255',
			    ]);
		    }
	    }

	    if ($profile->role_id == 3) {
		    $validation = array_merge($validation, [
			    'day'   => 'bail|required|array',
			    'day.*' => 'bail|required|numeric|exists:days,id',

			    'availability'             => 'bail|required|array',
			    'availability.*'           => 'bail|required|array',
			    'availability.*.available' => 'bail|sometimes|bool',
			    'availability.*.charges'   => 'bail|sometimes|nullable|numeric|min:1',
		    ]);
	    } else if ($profile->role_id == 4) {
		    $validation = array_merge($validation, [
			    'education'   => 'bail|required|array',
			    'education.*' => 'bail|required|numeric|exists:education,id',

			    'interest'   => 'bail|required|array',
			    'interest.*' => 'bail|required|numeric|exists:interests,id',

			    'fee'             => 'bail|required|numeric|min:1',
			    'service_charges' => 'bail|required|numeric|min:0',

			    'availability'     => 'bail|required|array',
				'availability.*' => 'bail|required|array',
				'availability.*.*' => 'bail|required|numeric|exists:time_types,id',
			]);
		} else if ($profile->role_id == 5) {
			$validation = array_merge($validation, [

			]);
		}


		$request->validate($validation, [
			'experience.required' => "You must have at least experienced in single field.",
		]);

		$data = $request->only([
			'firstname', 'lastname', 'email', 'phone', 'dob', 'gender', 'lat', 'long', 'country', 'city', 'postcode', 'address'
		]);
		$data['approved'] = $request->approved ?? false;
		$data['insured'] = $request->insured ?? false;
		$data['vaccinated'] = $request->vaccinated ?? false;

		//Reference Validation
		if (!in_array($profile->role_id, [1, 2])) {
			$data = array_merge($data, $request->only([
				'referee1_name',
				'referee1_email',
				'referee1_phone',
				'referee1_how_long',
				'referee1_how_contact',

				'referee2_name',
				'referee2_email',
				'referee2_phone',
			    'referee2_how_long',
			    'referee2_how_contact',
		    ]));
		    $data = array_merge($data, [
			    'referee1_country_id' => $request->referee1_country_id,
			    'referee2_country_id' => $request->referee2_country_id,
		    ]);

		    if ($profile->role_id == 4) {
			    $data = array_merge($data, $request->only([
				    'referee1_child_age',
				    'referee2_child_age'
			    ]));
		    }
	    }

	    if($profile->role_id > 2){
		    $data = array_merge($data, $request->only([
			    'years_experience', 'info', 'other'
		    ]));

		    $data['dbs'] = ($request->dbs == 'yes') ? true : false;

		    $data = array_merge($data, $request->only([
			    'dbs_type', 'dbs_issue', 'dbs_cert'
		    ]));
	    }

	    if ($request->hasFile('profile')) {
		    $path = $request->file('profile')->store('public/profile');
		    $path = 'storage/' . (explode('public/', $path)[1]);
		    $data['profile'] = $path;
	    }

	    if ($request->has('day')) {
		    $profile->days()->sync($request->day);
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

	    if ($request->has('skill')) {
		    $profile->skills()->sync($request->skill);
	    }

	    if ($request->has('interest')) {
		    $profile->interests()->sync($request->interest);
	    }

	    if ($profile->role_id == 3){
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
						    'charges'   => $availability['charges']
					    ]);
				    }
			    }
		    }
	    }elseif($profile->role_id == 4){

		    $data = array_merge($data, $request->only([
			    'fee', 'service_charges'
		    ]));
		    if ($request->has('availability')) {
			    $profile->time_availables()->delete();
			    foreach ($request->availability as $day => $avails) {
				    foreach ($avails as $time){
					    $profile->time_availables()->create([
						    'day_id'        => $day,
						    'type_id'       => $time
					    ]);
				    }
			    }
		    }
	    }

	    $profile->update($data);

	    return redirect()->back();
    }

    public function status(Request $request, User $user)
	{
		$user->status = $request->status;
		$user->save();
		return "success";
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
	    $user->delete();
	    session()->flash('notice', 'User has been removed!');
	    return redirect()->route('dashboard.user.index');
    }
}
