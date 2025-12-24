<?php

namespace App\Http\Controllers;

use App\Events\JobPostedEvent;
use App\Models\Contract;
use App\Models\Day;
use App\Models\Experience;
use App\Models\Language;
use App\Models\Role;
use App\Models\TimeAvailable;
use App\Models\TimeType;
use App\Models\User;
use App\Models\JobApplication;
use App\QueryFilter\SellerLocationFilter;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\QueryFilter\ContractActiveFilter;
use App\QueryFilter\JobTypeFilter;
use App\QueryFilter\JobLocationFilter;
use App\QueryFilter\JobExperienceFilter;
use Illuminate\Support\Facades\Hash;

class ContractController extends Controller
{
	public function __construct()
	{
		// Require authentication for viewing jobs (except create for posting)
		$this->middleware('auth')->except(['create']);
	}

	public function index(Request $request)
	{
		// Require authentication - no public job listings
		if (!auth()->check()) {
			// Store intended URL only for GET requests to avoid CSRF issues
			if ($request->isMethod('GET')) {
				session()->put('url.intended', $request->fullUrl());
			}
			return redirect()->route('login')->with('info', 'Please log in to view available jobs.');
		}

		$roles = Role::where('seller', true)->get();
		$experiences = Experience::all();

		$user = auth()->user();
		$userRole = $user->role->slug ?? '';
		$isAdmin = $user->role_id == 1 || $userRole === 'admin';
		$isClient = $userRole === 'client';
		$isCarer = in_array($userRole, ['carer', 'childminder', 'housekeeper']);

		// Get filter parameter (available or accepted)
		$filter = $request->get('filter', 'available'); // 'available' or 'accepted'

		// Start building query
		$query = Contract::with('user');

		// Apply role-based filtering
		if ($isClient) {
			// Clients only see their own jobs
			$query->where('user_id', $user->id);
		} elseif ($isAdmin) {
			// Admins see ALL jobs (both filled and available)
			// Filter by status if specified
			if ($filter === 'accepted') {
				$query->whereNotNull('filled_at');
			} elseif ($filter === 'available') {
				$query->whereNull('filled_at');
			}
		} elseif ($isCarer) {
			// Carers/Childminders/Housekeepers: Only see available jobs (not filled)
			$query->whereNull('filled_at');
			// Force filter to 'available' for service providers
			$filter = 'available';
		}

		// Apply filters through pipeline
		$contracts = app(Pipeline::class)->send($query)->through([
			ContractActiveFilter::class,
			JobTypeFilter::class,
			JobExperienceFilter::class,
			JobLocationFilter::class
		])->thenReturn()->paginate($request->count ?? 12);

		// Get counts for tabs
		$availableCount = 0;
		$acceptedCount = 0;
		
		if ($isClient || $isAdmin) {
			$baseQuery = Contract::query();
			if ($isClient) {
				$baseQuery->where('user_id', $user->id);
			}
			
			// Available count (not filled)
			$availableQuery = clone $baseQuery;
			$availableQuery->whereNull('filled_at');
			$availableCount = app(Pipeline::class)->send($availableQuery)->through([
				ContractActiveFilter::class,
				JobTypeFilter::class,
				JobExperienceFilter::class,
				JobLocationFilter::class
			])->thenReturn()->count();
			
			// Filled count (filled_at is not null)
			$filledQuery = clone $baseQuery;
			$filledQuery->whereNotNull('filled_at');
			$acceptedCount = app(Pipeline::class)->send($filledQuery)->through([
				ContractActiveFilter::class,
				JobTypeFilter::class,
				JobExperienceFilter::class,
				JobLocationFilter::class
			])->thenReturn()->count();
		}

		return view('app.pages.contract.index', compact('contracts', 'request', 'roles', 'experiences', 'isAdmin', 'isClient', 'isCarer', 'filter', 'availableCount', 'acceptedCount'));
	}

	public function create(Request $request, $type = null)
	{
		// Get type from route if not provided as parameter
		$type = $type ?? $request->route('type');

		$role = $types = $contract = $languages = $experiences = $educations = $interests = $skills = $days = $time_types = null;
		if ($type) {
			$role = Role::where('slug', $type)
				->where('seller', true)
				->where('active', true)
				->first();
		}
		if ($role) {
			$types = $role->types;
		}
		if ($request->isMethod('GET')) {
			if ($type && $request->tab !== 'choose-service') {
				if (auth()->id()) {
					$contract = Contract::where(['user_id' => auth()->id(), 'role_id' => $role->id, 'status' => 'pending'])->latest()->first();
					if ($request->tab == 'requirements') {
						$experiences = $role->experiences;
					}
					if ($request->tab !== 'requirements' && !$contract) {
						return back()->with(['type' => 'danger', 'notice' => "Whoops! something went wrong."]);
					}
					if ($request->tab == 'other') {
						$days = Day::all();
						$skills = $role->skills;
						$languages = Language::all();
						$time_types = TimeType::all();
						$interests = $role->interests;
						$educations = $role->educations;
					}

				}
				return view('app.pages.contract.create_new', compact('type', 'contract', 'role', 'types', 'languages', 'experiences', 'educations', 'interests', 'skills', 'days', 'time_types'));
			} else {
				if ($request->tab && $request->tab !== 'choose-service') {
					$request->merge(['tab' => 'choose-service']);
					session()->flash('type', 'danger');
					session()->flash('notice', 'You must choose service first!');
				}
				return view('app.pages.contract.create_new', compact('type'));
			}
		} elseif ($request->isMethod('POST')) {
			if (!auth()->id() && $request->tab == 'account') {
				$this->validate($request, [
					'firstname' => ['required', 'string', 'max:255'],
					'lastname' => ['required', 'string', 'max:255'],
					'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
					'phone' => ['required', 'string', 'max:255', 'unique:users,phone'],
					'password' => ['required', 'string', 'min:8', 'confirmed'],
				]);
				$user = User::create([
					'role_id' => 2,
					'status' => 'pending',
					'firstname' => $request->firstname,
					'lastname' => $request->lastname,
					'email' => $request->email,
					'phone' => $request->phone,
					'password' => Hash::make($request->password),
				]);
				event(new Registered($user));
				Auth::guard()->login($user);
				return redirect(route('contract.create', ['type' => $type]) . '?tab=requirements')->with(['notice' => "Account created successfully! Don't forget to verify e-mail."]);
			} elseif (auth()->id() && $request->tab == 'requirements') {
				$this->validate($request, [
					'contract' => 'nullable|numeric',
					'title' => 'bail|required|string',
					'experience' => 'bail|required|array',
					'experience.*' => 'bail|required|numeric|exists:experiences,id'
				]);
				if ($request->contract) {
					$contract = Contract::find($request->contract);

				}
				if (!$contract) {
					$contract = Contract::create(['user_id' => auth()->id(), 'role_id' => $role->id, 'title' => $request->title, 'company' => $request->company, 'gender' => $request->gender]);
					$contract->experiences()->attach($request->experience);
				} else {
					$contract->update(['title' => $request->title, 'company' => $request->company, 'gender' => $request->gender]);
					$contract->experiences()->sync($request->experience);
				}
				return redirect(route('contract.create', ['type' => $type]) . '?tab=schedule')->with(['notice' => ucfirst($type) . " requirements saved!."]);
			} elseif (auth()->id() && $request->tab == 'schedule') {
				$this->validate($request, [
					'contract' => 'required|numeric',
					'start_type' => 'bail|required|string|in:immediately,not-sure,specific-date',
					'start_date' => 'bail|sometimes|date',
					'end_type' => 'bail|required|string|in:fixed-period,on-going',
					'end_date' => 'bail|sometimes|date',
					'start_time' => 'bail|required|date_format:H:i',
					'end_time' => 'bail|required|date_format:H:i',
				]);
				$contract = Contract::findOrFail($request->contract);
				$contract->update([
					'start_type' => $request->start_type,
					'start_date' => $request->start_date ?? null,
					'end_type' => $request->end_type,
					'end_date' => $request->end_date ?? null,
					'start_time' => $request->start_time,
					'end_time' => $request->end_time
				]);
				return redirect(route('contract.create', ['type' => $type]) . '?tab=rate')->with(['notice' => ucfirst($type) . " time requirements saved!."]);
			} elseif (auth()->id() && $request->tab == 'rate') {
				$this->validate($request, [
					'contract' => 'required|numeric',
					'hourly_rate' => 'bail|required_without_all:daily_rate,weekly_rate',
					'daily_rate' => 'bail|required_without_all:hourly_rate,weekly_rate',
					'weekly_rate' => 'bail|required_without_all:daily_rate,hourly_rate',
					'desc' => 'bail|required|string|min:4|max:255',
				]);
				$contract = Contract::findOrFail($request->contract);
				$contract->update([
					'hourly_rate' => $request->hourly_rate,
					'daily_rate' => $request->daily_rate,
					'weekly_rate' => $request->weekly_rate,
					'desc' => $request->desc,
				]);
				return redirect(route('contract.create', ['type' => $type]) . '?tab=other')->with(['notice' => ucfirst($type) . " rate requirements saved!."]);
			} elseif (auth()->id() && $request->tab == 'other') {
				$data = [];
				$rules = [
					'contract' => 'required|numeric',
					'language' => 'bail|required|array',
					'language.*' => 'bail|required|numeric|exists:languages,id',
					'address' => 'bail|required|string|min:4',
					'lat' => 'bail|required|numeric',
					'long' => 'bail|required|numeric',
					'radius' => 'bail|required|numeric',
				];
				if ($role->id == 3) {
					$rules = array_merge($rules, [
						'type' => 'bail|required|array',
						'type.*' => 'bail|required|numeric|exists:types,id',
					]);
				} elseif ($role->id == 4) {
					$rules = array_merge($rules, [
						'drive' => 'bail|required|string|in:yes,no'
					]);
				} elseif ($role->id == 5) {
					$rules = array_merge($rules, [
						'how_often' => 'bail|required|string|in:Daily,Twice a week,Weekly,Every other week,Once a month,One time clean,Other',
						'beds' => 'bail|required|string|in:0 bedrooms,1 bedroom,2 bedrooms,3 bedrooms,4 bedrooms,5+ bedrooms,Studio',
						'baths' => 'bail|required|string|in:1 bathroom,1 bathroom + 1 additional toilet,2 bathrooms,2 bathrooms + 1 additional toilet,3 bathrooms,4+ bathrooms',
						'rooms' => 'bail|required|string|in:0,1,2,3,4+',
						'cleaning_type' => 'bail|required|string|in:Standard cleaning,Deep cleaning,Move-out cleaning',
					]);
				}
				if (in_array($role->id, [3, 5])) {
					$rules = array_merge($rules, [
						'day' => 'bail|required|array',
						'day.*' => 'bail|required|numeric|exists:days,id'
					]);
				} elseif ($role->id == 4) {
					$rules = array_merge($rules, [
						'availability' => 'bail|required|array',
						'availability.*' => 'bail|required|array',
						'availability.*.*' => 'bail|required|numeric|exists:time_types,id',

						'interest' => 'bail|required|array',
						'interest.*' => 'bail|required|numeric|exists:interests,id',
					]);
				}
				$request->validate($rules);

				$contract = Contract::findOrFail($request->contract);

				$data = array_merge($data, [
					'address' => $request->address,
					'lat' => $request->lat,
					'long' => $request->long,
					'radius' => $request->radius,
				]);


				if ($role->id == 3) {
					$data = array_merge($data, [
						'drive' => ($request->drive == 'yes') ? true : false,
					]);
				} elseif ($role->id == 5) {
					$data = array_merge($data, [
						'how_often' => $request->how_often,
						'beds' => $request->beds,
						'baths' => $request->baths,
						'rooms' => $request->rooms,
						'cleaning_type' => $request->cleaning_type,
					]);
				}
				$contract->update($data);
				if ($role->id == 3) {
					$contract->types()->sync($request->type);
				}

				if (in_array($role->id, [3, 5])) {
					$contract->days()->sync($request->day);
				} elseif ($role->id == 4) {
					if ($request->has('availability')) {
						foreach ($request->availability as $day => $avails) {
							foreach ($avails as $time) {
								$contract->time_availables()->create([
									'typeable_id' => $contract->id,
									'typeable_type' => 'Contract',
									'day_id' => $day,
									'type_id' => $time
								]);
							}
						}
					}

					$contract->interests()->sync($request->interest);
				}
				// approve jobs for admin
				if(auth()->user()->role_id == 1){

					$data = [
						'status' => 'active',
					];

					$contract->status = 'active';
					$contract->save();
				}

				$contract->languages()->sync($request->language);
				$contract->experiences()->sync($request->experience);
				event(new JobPostedEvent($contract));
				if(auth()->user()->role_id == 1){
				return redirect(route('dashboard'))->with(['notice' => "Contract has been published!"]);

				}
				return redirect(route('profile'))->with(['notice' => "Contract has been published!"]);
			}
		}
    }

    public function store(Request $request, $type)
    {
		$role = Role::where('slug', $type)
			->where('seller', true)
			->where('active', true)
			->firstOrFail();
		$uid = auth()->id();

	    $valid = [
			'title' => 'bail|required|string',
			'start_type' => 'bail|required|string|in:immediately,not-sure,specific-date',
			'start_date' => 'bail|sometimes|date',
			'end_type' => 'bail|required|string|in:fixed-period,on-going',
			'end_date' => 'bail|sometimes|date',
			'start_time' => 'bail|required|date_format:H:i',
			'end_time' => 'bail|required|date_format:H:i',
			'hourly_rate' => 'bail|required_without_all:daily_rate,weekly_rate',
			'daily_rate' => 'bail|required_without_all:hourly_rate,weekly_rate',
			'weekly_rate' => 'bail|required_without_all:daily_rate,hourly_rate',
			'desc' => 'bail|required|string|min:4|max:255',
		];

	    if($role->id == 3){
		    $valid = array_merge($valid, [
			    'type'  => 'bail|required|array',
			    'type.*' => 'bail|required|numeric|exists:types,id',
		    ]);
	    }elseif($role->id == 4){
	    	$valid = array_merge($valid, [
			    'drive'         => 'bail|required|string|in:yes,no'
		    ]);
	    }elseif($role->id == 5){
	    	$valid = array_merge($valid, [
	    		'how_often'     => 'bail|required|string|in:Daily,Twice a week,Weekly,Every other week,Once a month,One time clean,Other',
			    'beds'          => 'bail|required|string|in:0 bedrooms,1 bedroom,2 bedrooms,3 bedrooms,4 bedrooms,5+ bedrooms,Studio',
			    'baths'         => 'bail|required|string|in:1 bathroom,1 bathroom + 1 additional toilet,2 bathrooms,2 bathrooms + 1 additional toilet,3 bathrooms,4+ bathrooms',
			    'rooms'         => 'bail|required|string|in:0,1,2,3,4+',
			    'cleaning_type' => 'bail|required|string|in:Standard cleaning,Deep cleaning,Move-out cleaning',
		    ]);
	    }

	    if(in_array($role->id, [3,5])){
		    $valid = array_merge($valid, [
			    'day'  => 'bail|required|array',
				'day.*' => 'bail|required|numeric|exists:days,id'
		    ]);
	    }elseif($role->id == 4){
		    $valid = array_merge($valid, [
			    'availability'     => 'bail|required|array',
			    'availability.*'   => 'bail|required|array',
			    'availability.*.*' => 'bail|required|numeric|exists:time_types,id',

			    'interest'   => 'bail|required|array',
			    'interest.*' => 'bail|required|numeric|exists:interests,id',
		    ]);
	    }

	    $valid = array_merge($valid, [
		    'language'   => 'bail|required|array',
		    'language.*' => 'bail|required|numeric|exists:languages,id',
		    'experience'       => 'bail|required|array',
		    'experience.*'     => 'bail|required|numeric|exists:experiences,id',
	    ]);

    	$request->validate($valid);

	    $data = [
	    	'user_id'       => $uid,
		    'role_id'       => $role->id,
		    'title'         => $request->title,
		    'company'       => $request->company,
		    'start_type'    => $request->start_type,
		    'start_date'    => $request->start_date ?? null,
		    'end_type'      => $request->end_type,
		    'end_date'      => $request->end_date ?? null,
		    'start_time'    => $request->start_time,
		    'end_time'      => $request->end_time,
		    'hourly_rate'   => $request->hourly_rate,
		    'daily_rate'    => $request->daily_rate,
		    'weekly_rate'   => $request->weekly_rate,
		    'desc'          => $request->desc,
	    ];

	    if($role->id == 3){
		    $data = array_merge($data, [
			    'drive'         => ($request->drive == 'yes') ? true : false,
		    ]);
	    } elseif($role->id == 5){
	    	$data = array_merge($data, [
			    'how_often'     => $request->how_often,
			    'beds'          => $request->beds,
			    'baths'         => $request->baths,
			    'rooms'         => $request->rooms,
			    'cleaning_type' => $request->cleaning_type,
		    ]);
	    }

	    $contract = Contract::create($data);

	    if($role->id == 3){
	    	$contract->types()->sync($request->type);
	    }

	    if( in_array($role->id, [3,5]) ){
		    $contract->days()->sync($request->day);
	    }elseif($role->id == 4){
		    if ($request->has('availability')) {
			    foreach ($request->availability as $day => $avails) {
				    foreach ($avails as $time){
					    $contract->time_availables()->create([
						    'typeable_id'   => $contract->id,
						    'typeable_type'   => 'Contract',
						    'day_id'        => $day,
						    'type_id'       => $time
					    ]);
				    }
			    }
		    }

		    $contract->interests()->sync($request->interest);
	    }

	    $contract->languages()->sync($request->language);
	    $contract->experiences()->sync($request->experience);

	    event(new JobPostedEvent($contract));

	    session()->flash('notice', 'Contract has been published!');
	    return redirect()->route('contract.edit', ['contract' => $contract->id ]);
    }

    public function show(Contract $contract)
	{
		// Require authentication to view job details
		if (!auth()->check()) {
			// Store intended URL only for GET requests to avoid CSRF issues
			if (request()->isMethod('GET')) {
				session()->put('url.intended', request()->fullUrl());
			}
			return redirect()->route('login')->with('info', 'Please log in to view job details.');
		}

		$user = auth()->user();
		$userRole = $user->role->slug ?? '';
		$isAdmin = $user->role_id == 1 || $userRole === 'admin';
		$isClient = $userRole === 'client';
		$isCarer = in_array($userRole, ['carer', 'childminder', 'housekeeper']);

		// Check if user can view this job
		if ($isClient) {
			// Clients can only view their own jobs
			if ($contract->user_id !== $user->id) {
				abort(403, 'You can only view your own jobs.');
			}
		} elseif ($isCarer && !$isAdmin) {
			// Carers can only view jobs that haven't been filled
			if ($contract->filled_at) {
				abort(403, 'This job has already been filled.');
			}
		}
		// Admins can view all jobs (no restrictions)

		$days = Day::all();
		$time_types = TimeType::all();

		// Eager load user relationship to prevent N+1 queries and ensure user exists
		$contract->load('user');

		return view('app.pages.contract.show', compact('contract', 'days', 'time_types', 'isAdmin', 'isClient', 'isCarer'));
	}

    public function edit(Contract $contract)
    {
	    Gate::authorize('contract-owner', $contract);

    	$role           = $contract->role;
	    $types          = $role->types;
	    $languages      = Language::all();
	    $experiences    = $role->experiences;
	    $educations     = $role->educations;
	    $interests      = $role->interests;
	    $skills         = $role->skills;
	    $days           = Day::all();
	    $time_types     = TimeType::all();

        return view('app.pages.contract.edit', compact('contract', 'role', 'types', 'languages', 'experiences', 'educations', 'interests', 'skills', 'days', 'time_types'));
    }

    public function update(Request $request, Contract $contract)
    {
	    Gate::authorize('contract-owner', $contract);

    	$role = $contract->role;

	    $valid = [
		    'title' => 'bail|required|string',
		    'start_type'    => 'bail|required|string|in:immediately,not-sure,specific-date',
		    'start_date'    => 'bail|sometimes|date',
		    'end_type'      => 'bail|required|string|in:fixed-period,on-going',
		    'end_date'      => 'bail|sometimes|date',
		    'start_time'    => 'bail|required|date_format:H:i',
		    'end_time'      => 'bail|required|date_format:H:i',
		    'hourly_rate'   => 'bail|required|numeric',
		    'daily_rate'    => 'bail|required|numeric',
		    'weekly_rate'   => 'bail|required|numeric',
		    'desc'          => 'bail|required|string|min:4|max:255',
	    ];

	    if($role->id == 3){
		    $valid = array_merge($valid, [
			    'type'  => 'bail|required|array',
			    'type.*' => 'bail|required|numeric|exists:types,id',
		    ]);
	    }elseif($role->id == 4){
		    $valid = array_merge($valid, [
			    'drive'         => 'bail|required|string|in:yes,no'
		    ]);
	    }elseif($role->id == 5){
		    $valid = array_merge($valid, [
			    'how_often'     => 'bail|required|string|in:Daily,Twice a week,Weekly,Every other week,Once a month,One time clean,Other',
			    'beds'          => 'bail|required|string|in:0 bedrooms,1 bedroom,2 bedrooms,3 bedrooms,4 bedrooms,5+ bedrooms,Studio',
			    'baths'         => 'bail|required|string|in:1 bathroom,1 bathroom + 1 additional toilet,2 bathrooms,2 bathrooms + 1 additional toilet,3 bathrooms,4+ bathrooms',
			    'rooms'         => 'bail|required|string|in:0,1,2,3,4+',
			    'cleaning_type' => 'bail|required|string|in:Standard cleaning,Deep cleaning,Move-out cleaning',
		    ]);
	    }

	    if(in_array($role->id, [3,5])){
		    $valid = array_merge($valid, [
			    'day'  => 'bail|required|array',
			    'day.*' => 'bail|required|numeric|exists:days,id'
		    ]);
	    }elseif($role->id == 4){
		    $valid = array_merge($valid, [
			    'availability'     => 'bail|required|array',
			    'availability.*'   => 'bail|required|array',
			    'availability.*.*' => 'bail|required|numeric|exists:time_types,id',

			    'interest'   => 'bail|required|array',
			    'interest.*' => 'bail|required|numeric|exists:interests,id',
		    ]);
	    }

	    $valid = array_merge($valid, [
		    'language'   => 'bail|required|array',
		    'language.*' => 'bail|required|numeric|exists:languages,id',
		    'experience'       => 'bail|required|array',
		    'experience.*'     => 'bail|required|numeric|exists:experiences,id',
	    ]);

	    $request->validate($valid);

	    $data = [
		    'title'         => $request->title,
		    'company'       => $request->company,
		    'start_type'    => $request->start_type,
		    'start_date'    => $request->start_date ?? null,
		    'end_type'      => $request->end_type,
		    'end_date'      => $request->end_date ?? null,
		    'start_time'    => $request->start_time,
		    'end_time'      => $request->end_time,
		    'hourly_rate'   => $request->hourly_rate,
		    'daily_rate'    => $request->daily_rate,
		    'weekly_rate'   => $request->weekly_rate,
		    'desc'          => $request->desc,
	    ];

	    if($role->id == 3){
		    $data = array_merge($data, [
			    'drive'         => ($request->drive == 'yes') ? true : false,
		    ]);
	    } elseif($role->id == 5){
		    $data = array_merge($data, [
			    'how_often'     => $request->how_often,
			    'beds'          => $request->beds,
			    'baths'         => $request->baths,
			    'rooms'         => $request->rooms,
			    'cleaning_type' => $request->cleaning_type,
		    ]);
	    }

	    $contract->update($data);

	    if($role->id == 3){
		    $contract->types()->sync($request->type);
	    }

	    if( in_array($role->id, [3,5]) ){
		    $contract->days()->sync($request->day);
	    }elseif($role->id == 4){
		    if ($request->has('availability')) {
		    	$contract->time_availables()->delete();
			    foreach ($request->availability as $day => $avails) {
				    foreach ($avails as $time){
					    $contract->time_availables()->create([
						    'day_id'        => $day,
						    'type_id'       => $time
					    ]);
				    }
			    }
		    }

		    $contract->interests()->sync($request->interest);
	    }

	    $contract->languages()->sync($request->language);
	    $contract->experiences()->sync($request->experience);

	    session()->flash('notice', 'Contract has been published!');
	    return redirect()->route('contract.edit', ['contract' => $contract->id ]);
    }

    /**
     * Repost a filled job (create a new job with the same details)
     */
    public function repost(Contract $contract)
    {
        // Ensure client owns the job and it's not already filled
        if ($contract->user_id !== auth()->id()) {
            return back()->with('error', 'You can only repost your own jobs.');
        }

        // Create a new contract with the same details
        $newContract = $contract->replicate();
        $newContract->created_at = now();
        $newContract->updated_at = now();
        $newContract->status = 'pending'; // New jobs start as pending
        $newContract->filled_at = null;
        $newContract->filled_by_application_id = null;
        $newContract->reposted_at = now();
        $newContract->save();

        // Sync relationships (types, days, languages, experiences, educations, skills, interests, time_availables)
        $types = $contract->types->pluck('id')->toArray();
        if (!empty($types)) {
            $newContract->types()->sync($types);
        }
        
        $days = $contract->days->pluck('id')->toArray();
        if (!empty($days)) {
            $newContract->days()->sync($days);
        }
        
        $languages = $contract->languages->pluck('id')->toArray();
        if (!empty($languages)) {
            $newContract->languages()->sync($languages);
        }
        
        $experiences = $contract->experiences->pluck('id')->toArray();
        if (!empty($experiences)) {
            $newContract->experiences()->sync($experiences);
        }
        
        // Sync educations if the relationship exists
        if (method_exists($contract, 'educations')) {
            $educations = $contract->educations->pluck('id')->toArray();
            if (!empty($educations)) {
                $newContract->educations()->sync($educations);
            }
        }
        
        // Sync skills if the relationship exists
        if (method_exists($contract, 'skills')) {
            $skills = $contract->skills->pluck('id')->toArray();
            if (!empty($skills)) {
                $newContract->skills()->sync($skills);
            }
        }
        
        // Sync interests if the relationship exists
        if (method_exists($contract, 'interests')) {
            $interests = $contract->interests->pluck('id')->toArray();
            if (!empty($interests)) {
                $newContract->interests()->sync($interests);
            }
        }

        // Copy time_availables
        foreach ($contract->time_availables as $timeAvailable) {
            $newContract->time_availables()->create([
                'day_id' => $timeAvailable->day_id,
                'type_id' => $timeAvailable->type_id,
            ]);
        }

        // Dispatch JobPostedEvent for the new contract (only if status becomes active)
        // Note: The job will be posted when admin approves it (status changes to 'active')
        // But we can still dispatch the event if needed for immediate posting
        // event(new JobPostedEvent($newContract));

        return redirect()->route('contract.show', $newContract->id)->with('success', 'Job successfully reposted! It will be reviewed by administrators before going live.');
    }

    public function destroy(Contract $contract)
    {
	    Gate::authorize('contract-owner', $contract);

    	$contract->delete();
	    session()->flash('status', 'Contract has been removed!');

	    return redirect()->route('contract.index');
    }
}
