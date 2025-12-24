<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
	protected $twilioService;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(TwilioService $twilioService)
	{
		$this->twilioService = $twilioService;
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Contracts\Support\Renderable
	 */
	public function index()
	{
		$roles = Role::where('seller', true)
			->where('active', true)
			->get();
		$posts = Post::latest()->take(3)->get();
		// Use enhanced view if it exists, otherwise fallback to original
		if (view()->exists('app.pages.index-enhanced')) {
			return view('app.pages.index-enhanced', compact('roles', 'posts'));
		}
		return view('app.pages.index', compact('roles', 'posts'));
	}

	public function about()
	{
		return view('app.pages.about');
	}

	public function privacyPolicy(){
		return view('app.pages.privacy-policy');
	}

	public function termsCondition(){
		return view('app.pages.terms-condition');
	}

	public function cookiePolicy(){
		return view('app.pages.cookie-policy');
	}

	public function showInvoice(){
		return view('app.pages.invoice.show');
	}

	public function childcareDuringPendamic(){
		return view('app.pages.news.childcare-during-pendamic');
	}

	public function cronaVirusUpdate(){
		return view('app.pages.news.crona-virus-update');
	}

	public function selfEmployedCarers(){
		return view('app.pages.news.self-employed-carers');
	}

	public function editInvoice(){
		return view('app.pages.invoice.edit');
	}

	public function createHiring(){
		return view('app.pages.hiring.create');
	}

	public function helpdesk(){
		return view('app.pages.helpdesk.index');
	}

	public function hiringBoard(){
		return view('app.pages.hiring.board');
	}

	public function hourlyCare(){
		return view('app.pages.hourly-care');
	}

	public function howItWorks(){
		return view('app.pages.how-it-works');
	}

	/**
	 * Show page for clients
	 */
	public function forClients()
	{
		$roles = Role::where('seller', true)
			->where('active', true)
			->get();
		$posts = Post::latest()->take(3)->get();
		return view('app.pages.for-clients', compact('roles', 'posts'));
	}

	/**
	 * Show page for care providers
	 */
	public function forProviders()
	{
		$roles = Role::where('seller', true)
			->where('active', true)
			->get();
		$posts = Post::latest()->take(3)->get();
		return view('app.pages.for-providers', compact('roles', 'posts'));
	}

	public function jobApply(){
		return view('app.pages.job.apply');
	}

	public function jobBoard(){
		return view('app.pages.job.board');
	}

	public function jobManage(){
		return view('app.pages.job.manage');
	}

	public function team()
	{
		return view('app.pages.team');
	}

	public function sellerListing()
	{
		return view('app.pages.seller-listing');
	}

	public function verify(Request $request)
	{
		$this->validate($request, [
			'verification_code' => ['required', 'numeric']
		]);

		$user = auth()->user();

		// Use secure TwilioService for verification
		if ($this->twilioService->verifyCode($user->phone, $request->verification_code)) {
			$user->phone_verified_at = now();
			$user->save();
			return redirect('profile')->with(['type' => 'success', 'notice' => 'Phone number verified']);
		}

		return back()->with(['type' => 'danger', 'notice' => 'Invalid verification code entered!']);
	}

	public function resendOtp()
	{
		$user = auth()->user();

		if (!$user->phone) {
			return redirect()->route('verification.phone')->with([
				'type' => 'danger',
				'notice' => 'Phone number not found. Please update your profile with a valid phone number.'
			]);
		}

		// Use secure TwilioService for sending verification
		$result = $this->twilioService->sendVerificationCode($user->phone);
		
		if ($result['success']) {
			return redirect()->route('verification.phone')->with([
				'type' => 'success',
				'notice' => 'New OTP sent to your phone number: ' . $user->phone . '!'
			]);
		}

		return redirect()->route('verification.phone')->with([
			'type' => 'danger',
			'notice' => $result['message'] ?? 'Failed to send OTP. Please try again.'
		]);
	}
}
