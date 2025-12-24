<?php

namespace App\Http\Controllers;

use App\Mail\CareTraining;
use App\Mail\DbsCertificate;
use App\Mail\DbsTraining;
use App\Mail\DocumentInfo;
use App\Mail\ProfileCompletion;
use App\Mail\RefereeConfirmation;
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
use Illuminate\Support\Carbon;
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
		$this->middleware(['auth', 'verified', 'verified.phone']);
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
			// Use existing lat/long from database if not provided in request
			if (empty($request->lat) && !empty($profile->lat)) {
				$request->merge(['lat' => $profile->lat]);
			}
			if (empty($request->long) && !empty($profile->long)) {
				$request->merge(['long' => $profile->long]);
			}
			
			$validation = [
				'firstname' => 'bail|required|string|max:255',
				'lastname' => 'bail|required|string|max:255',
				'email' => 'bail|required|string|min:4|max:255|unique:users,email,' . $profile->id,
				'phone' => [
					'bail',
					'required',
					'string',
					'min:4',
					'max:255',
					'unique:users,phone,' . $profile->id,
					function ($attribute, $value, $fail) {
						// Remove all non-numeric characters except +
						$cleaned = preg_replace('/[^0-9+]/', '', $value);
						
						// Check if it's a UK number
						$isUK = false;
						if (substr($cleaned, 0, 3) === '+44') {
							$isUK = true;
						} elseif (substr($cleaned, 0, 1) === '0') {
							$isUK = true; // UK number starting with 0
						} elseif (substr($cleaned, 0, 2) === '44') {
							$isUK = true; // UK number with 44 but no +
						} elseif (strlen($cleaned) >= 10 && strlen($cleaned) <= 11 && substr($cleaned, 0, 1) !== '+') {
							$isUK = true; // UK number without country code
						}
						
						if (!$isUK && substr($cleaned, 0, 1) === '+') {
							$fail('Only UK phone numbers are accepted. Please use a UK number (e.g., +44 7700 900000 or 07700 900000).');
						}
					}
				],
				'gender' => 'bail|required|string|in:male,female',
				'address' => 'bail|required|string|min:4',
				'city' => 'bail|required|string|min:4|max:255',
				//'state'     => 'bail|required|string|min:4|max:255',
				'country' => 'bail|required|string|min:4|max:255',
				'postcode' => 'bail|required|string',
				'lat' => 'bail|nullable|numeric',
				'long' => 'bail|nullable|numeric',
			];
			if ($profile->role_id !== 2) {
				$validation['dob'] = 'bail|required|date';
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
					'has_ni_number' => 'bail|required|string|in:yes,no',
					'has_care_training' => 'bail|required|string|in:yes,no',
				];
				if (($request->dbs ?? 'no') == 'yes') {
					$validation = array_merge($validation, [
						'dbs_type' => 'bail|required|string|in:basic,standard,enhanced',
						'dbs_issue' => 'bail|required|date',
						'dbs_cert' => 'bail|required|string|min:4|max:255',
					]);
				}
				if (($request->has_ni_number ?? 'no') == 'yes') {
					$validation = array_merge($validation, [
						'ni_number' => 'bail|required|string',
					]);
				}
				$validation = array_merge($validation, [
					'referee1_name' => 'bail|required|string|max:255',
					'referee1_email' => 'bail|required|email|min:4|max:255',
					'referee1_phone' => 'bail|required|string|min:4|max:255',
					'referee1_country_id' => 'bail|required|numeric|exists:countries,id',
					'referee1_how_long' => 'bail|required|string|max:255',
					'referee1_how_contact' => 'bail|required|string|min:4|max:255',

					'referee2_name' => 'bail|required|string|max:255',
					'referee2_email' => 'bail|required|email|min:4|max:255',
					'referee2_phone' => 'bail|required|string|min:4|max:255',
					'referee2_country_id' => 'bail|required|numeric|exists:countries,id',
					'referee2_how_long' => 'bail|required|string|max:255',
					'referee2_how_contact' => 'bail|required|string|min:4|max:255',
				]);
			}
		}
		$request->validate($validation);

		// Convert string values to boolean for DBS, Care Training, and NI Number fields
		// Only process if these fields are present in the request (i.e., references tab)
		if ($request->has('dbs')) {
			($request->dbs && $request->dbs == 'yes') ? $request->merge(['dbs' => true]) : $request->merge(['dbs' => false]);
		}
		if ($request->has('has_care_training')) {
			($request->has_care_training && $request->has_care_training == 'yes') ? $request->merge(['has_care_training' => true]) : $request->merge(['has_care_training' => false]);
		}
		if ($request->has('has_ni_number')) {
			($request->has_ni_number && $request->has_ni_number == 'yes') ? $request->merge(['has_ni_number' => true]) : $request->merge(['has_ni_number' => false]);
		}
		
		// Send DBS Training email only to Carers/Housekeepers (role_id > 2) on references tab
		// Only send if they checked "no" for DBS certificate and profile not completed
		if ($type == 'references' && $profile->role_id > 2 && $request->has('dbs') && $request->dbs == false && $profile->profile_completed_at == null) {
			Mail::to($profile)->send(new DbsTraining($profile));
		}
		
		// Send Care Training email only to Carers/Housekeepers (role_id > 2) on references tab
		// Only send if they checked "no" for Care Training and profile not completed
		if ($type == 'references' && $profile->role_id > 2 && $request->has('has_care_training') && $request->has_care_training == false && $profile->profile_completed_at == null) {
			Mail::to($profile)->send(new CareTraining($profile));
		}

		$data = $request->except(['_token']);
		
		// Preserve existing lat/long if not provided and address hasn't changed
		if (empty($data['lat']) && !empty($profile->lat)) {
			$data['lat'] = $profile->lat;
		}
		if (empty($data['long']) && !empty($profile->long)) {
			$data['long'] = $profile->long;
		}

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
				if (is_null($profile->profile_completed_at) ) {
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
					if ($request->dbs && $request->dbs == 'yes') {
						Mail::to($user)->send(new DbsCertificate($profile));
					}
				}


				// send mail to first refree
				$data = [
					'referee' => 1,
				];
				Mail::to($profile->referee1_email)->send(new RefereeConfirmation($profile, $data));
				// send mail to second refree
				$data = [
					'referee' => 2,
				];
				Mail::to($profile->referee2_email)->send(new RefereeConfirmation($profile, $data));

				$profile->profile_completed_at = now();
				$profile->save();
			} elseif ($profile->profile_completed_at) {


				if ($profile->referee1_status == false) {
					// send mail to first refree
					$data = [
						'referee' => 1,
					];
					Mail::to($profile->referee1_email)->send(new RefereeConfirmation($profile, $data));

				}
				if ($profile->referee2_status == false) {
					// send mail to second refree
					$data = [
						'referee' => 2,
					];
					Mail::to($profile->referee2_email)->send(new RefereeConfirmation($profile, $data));
				}
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

	public function photo()
	{
		$profile = auth()->user();
		// Use enhanced view if it exists, otherwise fallback to original
		if (view()->exists('app.pages.profile.photo-enhanced')) {
			return view('app.pages.profile.photo-enhanced', compact('profile'));
		}
		return view('app.pages.profile.photo', compact('profile'));
	}

	public function storePhoto(Request $request)
	{
		// Security: Rate limiting (handled by middleware)
		// Security: CSRF protection (handled by middleware)
		
		$profile = auth()->user();
		
		// SECURITY: Check if profile is locked
		if ($profile->profile_locked) {
			return response()->json([
				'error' => 'Profile photo is locked and cannot be changed. Please contact admin if you need to update it.',
				'locked' => true
			], 403);
		}
		
		$request->validate([
			'profile' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120|dimensions:min_width=200,min_height=200,max_width=5000,max_height=5000',
		]);

		if (!$request->hasFile('profile')) {
			return response()->json(['error' => 'No file uploaded'], 400);
		}

		$file = $request->file('profile');
		
		// Security: Sanitize filename
		$originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
		$sanitizedName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
		$extension = $file->getClientOriginalExtension();
		$fileName = $sanitizedName . '_' . time() . '_' . substr(md5($profile->id), 0, 8) . '.' . $extension;
		
		// Store file
		$path = $file->storeAs('public/profile', $fileName);
		$path = 'storage/' . (explode('public/', $path)[1]);
		
		$profile->profile = $path;
		// Reset verification status when new photo is uploaded (before locking)
		$profile->profile_verified_at = null;
		$profile->profile_verification_id = null;
		$profile->save();
		
		return response()->json([
			'success' => true,
			'path' => asset($path),
			'message' => 'Profile photo uploaded successfully. Please note: Once locked by admin, you will not be able to change it.',
			'locked' => false
		]);
	}

	public function video()
	{
		$profile = auth()->user();
		// Use enhanced view if it exists, otherwise fallback to original
		if (view()->exists('app.pages.profile.video-enhanced')) {
			return view('app.pages.profile.video-enhanced', compact('profile'));
		}
		return view('app.pages.profile.video', compact('profile'));
	}

	public function storeVideo(Request $request)
	{
		// Security: Rate limiting (handled by middleware)
		// Security: CSRF protection (handled by middleware)
		
		$profile = auth()->user();
		
		// Handle video removal
		if ($request->action && $request->action == 'remove' && $profile->video) {
			if (Storage::disk('s3')->delete($profile->video)) {
				$profile->video = null;
				$profile->save();
				return response()->json([
					'success' => true,
					'path' => null,
					'message' => 'Video removed successfully'
				]);
			}
			return response()->json(['error' => 'Failed to remove video'], 500);
		}
		
		// Validate video upload
		$request->validate([
			'video' => 'required|file|mimes:mp4,avi,mov,wmv,flv,webm|max:8192', // Max 8MB
		], [
			'video.required' => 'Please select a video file',
			'video.mimes' => 'Video must be in MP4, AVI, MOV, WMV, FLV, or WebM format',
			'video.max' => 'Video size must not exceed 8MB',
		]);

		if (!$request->hasFile('video')) {
			return response()->json(['error' => 'No video file uploaded'], 400);
		}

		$file = $request->file('video');
		
		// Security: Sanitize filename
		$originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
		$sanitizedName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
		$extension = $file->getClientOriginalExtension();
		$fileName = $sanitizedName . '_' . time() . '_' . substr(md5($profile->id), 0, 8) . '.' . $extension;

		$filePath = 'video-profile/' . $profile->firstname . '_' . $profile->lastname . '/' . $fileName;

		// Security: Validate file content (basic check)
		$mimeType = $file->getMimeType();
		$allowedMimes = ['video/mp4', 'video/avi', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv', 'video/x-flv', 'video/webm'];
		if (!in_array($mimeType, $allowedMimes)) {
			return response()->json(['error' => 'Invalid video file type'], 400);
		}

		try {
			$path = Storage::disk('s3')->put($filePath, file_get_contents($file->getRealPath()));

			$profile->video = $filePath;
			$profile->save();

			$videoUrl = Storage::disk('s3')->temporaryUrl($profile->video, Carbon::now()->addMinutes(60));
			
			return response()->json([
				'success' => true,
				'path' => $videoUrl,
				'message' => 'Video uploaded successfully'
			]);
		} catch (\Exception $e) {
			\Log::error('Video upload failed: ' . $e->getMessage());
			return response()->json(['error' => 'Failed to upload video. Please try again.'], 500);
		}
	}

	public function ratings()
	{
		$profile = auth()->user();
		$reviews = $profile->reviews;
		return view('app.pages.profile.ratings', compact('profile', 'reviews'));
	}

	public function storeRatings()
	{

	}

	public function documents()
	{
		$profile = auth()->user();
		$documents = Document::where('user_id', $profile->id)->get();
		return view('app.pages.profile.documents', compact('profile', 'documents'));
	}

	public function storeDocuments(Request $request)
	{
		$profile = auth()->user();

		// SECURITY: Validate document uploads
		$validation = [
			'doc' => 'required|array',
			'doc.*.name' => 'required|string|min:2|max:255',
			'doc.*.expiration' => 'nullable|date',
			'doc.*.file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // Max 10MB
			'doc.*.id' => 'nullable|integer|exists:documents,id',
		];

		$request->validate($validation);

		$ids = [];

		if ($request->has('doc')) {
			foreach ($request->doc as $k => $val) {
				// SECURITY: Sanitize document name
				$documentName = strip_tags($val['name'] ?? '');
				$documentName = preg_replace('/[^a-zA-Z0-9\s\-_\.]/', '', $documentName);
				$documentName = substr($documentName, 0, 255); // Limit length
				
				$data = [
					'name' => $documentName,
					'expiration' => $val['expiration'] ? Carbon::parse($val['expiration'])->toDateString() : null,
				];

				if ($request->hasFile('doc.' . $k . '.file')) {
					$file = $request->file('doc.' . $k . '.file');
					
					// SECURITY: Validate file type by MIME type
					$allowedMimes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/jpg', 'image/png'];
					$mimeType = $file->getMimeType();
					if (!in_array($mimeType, $allowedMimes)) {
						continue; // Skip invalid files
					}

					// SECURITY: Sanitize filename
					$originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
					$sanitizedName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
					$extension = $file->getClientOriginalExtension();
					$fileName = $sanitizedName . '_' . time() . '_' . substr(md5($profile->id . $k), 0, 8) . '.' . $extension;
					
					// SECURITY: Sanitize file path
					$safeFirstName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $profile->firstname);
					$safeLastName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $profile->lastname);
					$filePath = '/' . $safeFirstName . '_' . $safeLastName . '/' . $fileName;

					$path = Storage::disk('s3')->put($filePath, file_get_contents($file->getRealPath()));

					$data['name'] = $fileName;
					$data['path'] = $filePath;


					// $path = $request->file('doc.' . $k . '.file')
					// 	->store('public/document');
					// $data['path'] = 'storage/' . (explode('public/', $path)[1]);
					//$request->allFiles('doc');
				}

				//$path = $val->file->store('public/document');

				if ($val['id'] ?? false) {
					$ids[] = $val['id'];
					$document = $profile->documents()->where('id', $val['id'])->first();
					if ($document) {
						$document->update($data);
						$document->updateComplianceStatus();
						$document->save();
					}
				} else {
					$d = $profile->documents()->create($data);
					$d->updateComplianceStatus();
					$d->save();
					if ($d->path) {
						$users = User::where(['role_id' => 1, 'status' => 'active'])->get();
						foreach ($users as $user) {
							Mail::to($user)->send(new DocumentInfo($d));
						}
					}
					$ids[] = $d->id;
				}
			}
		}

		$profile->documents()->whereNull('path')->delete();

		return redirect()->back();
	}


}
