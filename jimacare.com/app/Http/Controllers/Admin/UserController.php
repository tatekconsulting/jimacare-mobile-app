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
use App\Models\Timesheet;
use App\Models\JobApplication;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\TemporaryPassword;
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
		// Build base query - exclude admins
		$query = User::where('role_id', '!=', 1);
		
		// Apply filters
		$where = [];
		if ($request->type) {
			$where['role_id'] = $request->type;
			$query->where('role_id', $request->type);
		}
		if ($request->status) {
			$where['status'] = $request->status;
			$query->where('status', $request->status);
		}
		
		// Advanced filters
		if ($request->city) {
			$query->where('city', 'like', '%' . $request->city . '%');
		}
		if ($request->country) {
			$query->where('country', 'like', '%' . $request->country . '%');
		}
		if ($request->dbs) {
			$query->where('dbs', $request->dbs == 'yes' ? true : false);
		}
		if ($request->verified_video) {
			$query->where('verified_video', $request->verified_video == 'yes' ? true : false);
		}
		if ($request->date_from) {
			$query->whereDate('created_at', '>=', $request->date_from);
		}
		if ($request->date_to) {
			$query->whereDate('created_at', '<=', $request->date_to);
		}
		if ($request->last_login_from) {
			$query->whereDate('last_login', '>=', $request->last_login_from);
		}
		if ($request->last_login_to) {
			$query->whereDate('last_login', '<=', $request->last_login_to);
		}
		
		// Search functionality
		if ($request->name) {
			$query->where(function ($q) use ($request) {
				$q->where('firstname', 'like', '%' . $request->name . '%')
					->orWhere('lastname', 'like', '%' . $request->name . '%')
					->orWhere('phone', 'like', '%' . $request->name . '%')
					->orWhere('email', 'like', '%' . $request->name . '%');
			});
		}
		
		// Get statistics
		$stats = [
			'total' => User::where('role_id', '!=', 1)->count(),
			'active' => User::where('role_id', '!=', 1)->where('status', 'active')->count(),
			'pending' => User::where('role_id', '!=', 1)->where('status', 'pending')->count(),
			'review' => User::where('role_id', '!=', 1)->where('status', 'review')->count(),
			'blocked' => User::where('role_id', '!=', 1)->where('status', 'block')->count(),
			'new_this_month' => User::where('role_id', '!=', 1)
				->whereMonth('created_at', Carbon::now()->month)
				->whereYear('created_at', Carbon::now()->year)
				->count(),
		];
		
		// Get paginated results - only load role relationship to avoid errors
		// Handle "all" option to show all users without pagination
		$perPage = $request->per_page ?? 15;
		if ($perPage === 'all') {
			// Get all users without pagination
			$users = $query->with('role')
				->orderBy('created_at', 'desc')
				->get();
			
			// Create a custom paginator-like object for compatibility
			$users = new \Illuminate\Pagination\LengthAwarePaginator(
				$users,
				$users->count(),
				$users->count(),
				1,
				['path' => $request->url(), 'query' => $request->query()]
			);
		} else {
			$users = $query->with('role')
				->orderBy('created_at', 'desc')
				->paginate((int) $perPage);
		}
		
		// Add counts manually to avoid relationship errors
		foreach ($users as $user) {
			try {
				if (Schema::hasTable('timesheets')) {
					$user->timesheets_count = Timesheet::where('carer_id', $user->id)->count();
				} else {
					$user->timesheets_count = 0;
				}
			} catch (\Exception $e) {
				$user->timesheets_count = 0;
			}
			
			try {
				if (Schema::hasTable('job_applications')) {
					$user->job_applications_count = JobApplication::where('carer_id', $user->id)->count();
				} else {
					$user->job_applications_count = 0;
				}
			} catch (\Exception $e) {
				$user->job_applications_count = 0;
			}
		}
		
		// Get unpaginated for export (without relationships to avoid errors)
		$unpaginatedusers = $query->get();
		
		$roles = Role::where('slug', '<>', 'admin')->get();
		
		return view('admin.pages.user.index', compact('users', 'roles', 'unpaginatedusers', 'stats'));
	}
	public function admins(Request $request)
	{
		$users = User::where('role_id', 1)->where('power_admin', false)->paginate(15);

		$roles = Role::where('slug', '<>', 'admin')->get();
		return view('admin.pages.admin.index', compact('users', 'roles'));
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
		// get previous user id
		$previous = User::where('id', '<', $user->id)->max('id');

		// get next user id
		$next = User::where('id', '>', $user->id)->min('id');
		$countries = Country::all();
		$types = Type::where('role_id', $profile->role_id)->get();
		$skills = Skill::where('role_id', $profile->role_id)->get();
		$interests = Interest::where('role_id', $profile->role_id)->get();
		$experiences = Experience::where('role_id', $profile->role_id)->get();
		$educations = Education::where('role_id', $profile->role_id)->get();
		$documents = Document::where('user_id', $profile->id)->get();
		$days = Day::all();
		$time_types = TimeType::all();


		return view('admin.pages.user.edit', compact('profile', 'documents', 'countries', 'types', 'skills', 'interests', 'experiences', 'educations', 'days', 'time_types','previous','next'));
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

			'gender' => 'bail|required|string|in:male,female',
			'verified_video' => 'bail|required|string|in:true,false',
			'city' => 'bail|required|string|min:4|max:255',
			//'state'     => 'bail|required|string|min:4|max:255',
			'country' => 'bail|required|string',
			'postcode' => 'bail|required|string',
			'role' => 'bail|required|string',
			'address' => 'bail|required|string|min:4',

			'language' => 'bail|required|array',
			'language.*' => 'bail|required|numeric|exists:languages,id',

		];
		if ($profile->role_id !== 2) {
			$validation = array_merge($validation, [
				'dob' => 'bail|required|date',
			]);
		}

		if ($profile->role_id > 2) {
			$validation = array_merge($validation, [
				'years_experience' => 'bail|required|numeric',
				'info' => 'bail|required|string|min:4',
				'other' => 'bail|required|string|min:4',

				//Experience Validation
				'experience' => 'bail|required|array',
				'experience.*' => 'bail|required|numeric|exists:experiences,id',

				//Skills
				'skill' => 'bail|required|array',
				'skill.*' => 'bail|required|numeric|exists:skills,id',

				//DBS Validation
				'dbs' => 'bail|required|string|in:yes,no',
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
				'referee1_status' => 'bail|required',
				'referee1_email' => 'bail|required|string|min:4|max:255',
				'referee1_phone' => 'bail|required|string|min:4|max:255',
				'referee1_country_id' => 'bail|required|numeric|exists:countries,id',
				'referee1_how_long' => 'bail|required|string|min:4|max:255',
				'referee1_how_contact' => 'bail|required|string|min:4|max:255',

				'referee2_name' => 'bail|required|string|max:255',
				'referee2_status' => 'bail|required',
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
				'day' => 'bail|required|array',
				'day.*' => 'bail|required|numeric|exists:days,id',

				'availability' => 'bail|required|array',
				'availability.*' => 'bail|required|array',
				'availability.*.available' => 'bail|sometimes|bool',
				'availability.*.charges' => 'bail|sometimes|nullable|numeric|min:1',
			]);
		} else if ($profile->role_id == 4) {
			$validation = array_merge($validation, [
				'education' => 'bail|required|array',
				'education.*' => 'bail|required|numeric|exists:education,id',

				'interest' => 'bail|required|array',
				'interest.*' => 'bail|required|numeric|exists:interests,id',

				'fee' => 'bail|required|numeric|min:1',
				'service_charges' => 'bail|required|numeric|min:0',

				'availability' => 'bail|required|array',
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
			'firstname',
			'lastname',
			'email',
			'phone',
			'dob',
			'gender',
			'lat',
			'long',
			'country',
			'city',
			'postcode',
			'address',
		]);
		$data['approved'] = $request->approved ?? false;
		$data['verified_video'] = $request->verified_video == 'true' ? true : false;

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
				'referee1_status' => $request->referee1_status == "true" ? true : false,
				'referee2_status' => $request->referee2_status == "true" ? true : false,
			]);

			if ($profile->role_id == 4) {
				$data = array_merge($data, $request->only([
					'referee1_child_age',
					'referee2_child_age'
				]));
			}
		}

		if ($profile->role_id > 2) {
			$data = array_merge($data, $request->only([
				'years_experience',
				'info',
				'other'
			]));

			$data['dbs'] = ($request->dbs == 'yes') ? true : false;

			$data = array_merge($data, $request->only([
				'dbs_type',
				'dbs_issue',
				'dbs_cert'
			]));
		}

		// Admins can always update profile photos (even if locked)
		if ($request->hasFile('profile')) {
			$path = $request->file('profile')->store('public/profile');
			$path = 'storage/' . (explode('public/', $path)[1]);
			$data['profile'] = $path;
			// Reset verification when admin changes photo
			$data['profile_verified_at'] = null;
			$data['profile_verification_id'] = null;
		}
		
		// Handle profile lock/unlock
		if ($request->has('profile_locked')) {
			$data['profile_locked'] = $request->profile_locked == '1' || $request->profile_locked === true;
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
		} elseif ($profile->role_id == 4) {

			$data = array_merge($data, $request->only([
				'fee',
				'service_charges'
			]));
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

		// update role
		$data['role_id'] = $request->role;

		$profile->update($data);

		return redirect()->back();
	}

	public function status(Request $request, User $user)
	{
		$user->status = $request->status;
		$user->save();
		return response()->json(['success' => true, 'message' => 'Status updated successfully']);
	}
	
	/**
	 * Bulk update user status
	 */
	public function bulkStatus(Request $request)
	{
		$request->validate([
			'user_ids' => 'required|array',
			'user_ids.*' => 'exists:users,id',
			'status' => 'required|in:pending,review,active,block'
		]);
		
		$count = User::whereIn('id', $request->user_ids)
			->where('power_admin', false)
			->update(['status' => $request->status]);
		
		return response()->json([
			'success' => true,
			'message' => "Status updated for {$count} user(s)"
		]);
	}
	
	/**
	 * Bulk delete users
	 */
	public function bulkDelete(Request $request)
	{
		$request->validate([
			'user_ids' => 'required|array',
			'user_ids.*' => 'exists:users,id'
		]);
		
		$count = User::whereIn('id', $request->user_ids)
			->where('power_admin', false)
			->delete();
		
		return response()->json([
			'success' => true,
			'message' => "{$count} user(s) deleted successfully"
		]);
	}
	
	/**
	 * Reset user password (Admin only)
	 * Generates a temporary password and sends it to the user via email
	 * 
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Models\User  $user
	 * @return \Illuminate\Http\Response
	 */
	public function resetPassword(Request $request, User $user)
	{
		// Prevent resetting power admin passwords
		if ($user->power_admin) {
			return response()->json([
				'success' => false,
				'message' => 'Cannot reset password for power admin accounts'
			], 403);
		}
		
		// Generate a secure temporary password (12 characters: letters, numbers, and symbols)
		$temporaryPassword = Str::random(12);
		
		// Update the user's password with temporary password
		$user->password = Hash::make($temporaryPassword);
		$user->must_change_password = true;
		$user->save();
		
		// Send temporary password via email
		$emailSent = false;
		$emailError = null;
		try {
			Mail::to($user->email)->send(new TemporaryPassword($user, $temporaryPassword));
			$emailSent = true;
		} catch (\Exception $e) {
			$emailError = $e->getMessage();
			\Log::error('Failed to send temporary password email', [
				'user_id' => $user->id,
				'user_email' => $user->email,
				'error' => $emailError,
				'trace' => $e->getTraceAsString()
			]);
		}
		
		// If email failed, return success with the temporary password so admin can share it manually
		if (!$emailSent) {
			return response()->json([
				'success' => true,
				'warning' => true,
				'message' => 'Temporary password generated successfully, but email failed to send. Please share this password with the user manually: ' . $temporaryPassword,
				'temporary_password' => $temporaryPassword,
				'user_email' => $user->email,
				'error_details' => $emailError
			]);
		}
		
		return response()->json([
			'success' => true,
			'message' => 'Temporary password has been generated and sent to ' . $user->email
		]);
	}

	public function exportUsers(Request $request)
	{
		// SECURITY: Explicit authorization check
		if (!auth()->check() || auth()->user()->role_id != 1) {
			abort(403, 'Unauthorized access. Admin privileges required.');
		}

		// SECURITY: Validate input - ensure user IDs are provided and are integers
		$request->validate([
			'users' => 'required|array|min:1|max:1000', // Limit to 1000 users to prevent DoS
			'users.*' => 'required|integer|exists:users,id', // Validate each ID is integer and exists
		]);

		// SECURITY: Additional check - ensure we're not exporting admins (unless current user is power admin)
		$userIds = $request->users;
		$query = User::whereIn('id', $userIds);
		
		// If not power admin, exclude power admins from export
		if (!auth()->user()->power_admin) {
			$query->where(function($q) {
				$q->where('power_admin', false)
				  ->orWhereNull('power_admin');
			});
		}

		$users = $query->with('role')->get();

		// SECURITY: Audit logging - track who exported what
		\Log::info('User data exported', [
			'admin_id' => auth()->id(),
			'admin_email' => auth()->user()->email,
			'user_count' => count($userIds),
			'user_ids' => $userIds,
			'timestamp' => now()->toDateTimeString(),
		]);

		$fileName = 'Users-list-' . date('Y-m-d-His') . '.csv';

		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=$fileName",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0"
		);

		$columns = array('Type', 'Full Name', 'Date of Birth','Email', 'Phone', 'National Insurance Number',
        'Address', 'City', 'Postcode', 'Country', 'Profile Photo', 'Reference 1 Status', 'Reference 2 Status', 'DBS', 'Care Training', 'Profile', 'Last Login',
        'Last Profile Updated', 'Member Since', 'Status');

		$callback = function () use ($users, $columns) {
			$file = fopen('php://output', 'w');
			fputcsv($file, $columns);

			foreach ($users as $user) {
				$row['Type'] = $user->role->title ?? 'N/A';
				$row['Full Name'] = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));
				$row['Date of Birth'] = ($user->dob) ? Carbon::parse($user->dob)->format('d M Y') : 'N/A';
				$row['Email'] = $user->email ?? 'N/A';
				$row['Phone'] = $user->phone ?? 'N/A';
				$row['National Insurance Number'] = $user->ni_number ?? 'N/A';
				$row['Address'] = $user->address ?? 'N/A';
				$row['City'] = $user->city ?? 'N/A';
				$row['Postcode'] = $user->postcode ?? 'N/A';
				$row['Country'] = $user->country ?? 'N/A';
				// Check if profile photo is uploaded
				// Profile photo is stored in the 'profile' field
				$hasProfilePhoto = !empty($user->profile) && trim($user->profile) !== '';
				$row['Profile Photo'] = $hasProfilePhoto ? 'Yes' : 'No';
				$row['Reference 1 Status'] = ($user->referee1_status) ? 'Completed' : 'Pending';
				$row['Reference 2 Status'] = ($user->referee2_status) ? 'Completed' : 'Pending';
				$row['DBS'] = ($user->dbs) ? 'Yes' : 'No';
				$row['Care Training'] = ($user->has_care_training) ? 'Yes' : 'No';
				$row['Profile'] = (isset($user->profile_completed_at)) ? 'Completed' : 'Pending';
				$row['Last Login'] = ($user->last_login) ? $user->last_login->format('d M Y H:i:s') : 'N/A';
				$row['Last Profile Updated'] = ($user->updated_at) ? $user->updated_at->format('d M Y H:i:s') : 'N/A';
				if ($user->created_at ?? false)
					$row['Member Since'] = $user->created_at->format('d M Y');
				else
					$row['Member Since'] = 'N/A';

				foreach (['pending', 'review', 'active', 'block'] as $s) {
					if ($s == $user->status) {
						$row['Status'] = ucfirst($s);
					}
				}
				fputcsv($file, array_values($row));
			}

			fclose($file);
		};

		return response()->stream($callback, 200, $headers);
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
