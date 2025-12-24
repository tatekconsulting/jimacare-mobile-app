<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Role;
use Illuminate\Http\Request;

class HomeController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	/*public function __construct()
	{
		$this->middleware('auth');
	}*/

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

	public function jobApply(){
		return view('app.pages.job.apply');
	}

	public function jobBoard(){
		return view('app.pages.job.board');
	}

	public function jobManage(){
		return view('app.pages.job.manage');
	}

	public function team(){
		return view('app.pages.team');
	}

	public function sellerListing(){
		return view('app.pages.seller-listing');
	}
}
