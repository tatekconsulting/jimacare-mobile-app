<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Experience;
use App\Models\Role;
use App\Models\TimeType;
use App\Models\User;
use App\QueryFilter\ActiveFilter;
use App\QueryFilter\SellerTypeFilter;
use App\QueryFilter\SellerLocationFilter;
use App\QueryFilter\SellerExperienceFilter;
use App\Services\SellerRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		// Require authentication - no public seller listings
		if (!auth()->check()) {
			// Store intended URL only for GET requests to avoid CSRF issues
			if ($request->isMethod('GET')) {
				session()->put('url.intended', $request->fullUrl());
			}
			return redirect()->route('login')->with('info', 'Please log in to search for carers, childminders, or housekeepers.');
		}

		$user = auth()->user();
		$userRole = $user->role->slug ?? '';
		$isAdmin = $user->role_id == 1 || $userRole === 'admin';
		$isClient = $userRole === 'client';
		$isServiceProvider = in_array($userRole, ['carer', 'childminder', 'housekeeper']);

		// Only Clients and Admins can search for sellers
		if (!$isAdmin && !$isClient) {
			$roleTitle = $user->role->title ?? 'Service Provider';
			return redirect()->route('contract.index')
				->with('info', "As a {$roleTitle}, you can browse available jobs instead. Only clients can search for service providers.");
		}
        
		$roles = Role::where('seller', true)->get();
		$experiences = Experience::all();
		
		// Build query - exclude service providers of the same role (if not admin)
		$query = User::query();
		
		// Filter out users of the same role as the logged-in user (if they're a service provider)
		// This shouldn't happen since only clients/admins can access, but adding for safety
		if ($isServiceProvider && !$isAdmin) {
			$query->where('role_id', '!=', $user->role_id);
		}
		
		$users = app(Pipeline::class)->send($query)->through([
			ActiveFilter::class,
			SellerTypeFilter::class,
			SellerExperienceFilter::class,
			SellerLocationFilter::class
		])->thenReturn()
		->with(['role', 'experiences', 'educations', 'reviews', 'languages', 'skills', 'days', 'documents'])
		->get();

		// Get client location if available
		$clientLat = $request->lat ?? $user->lat ?? null;
		$clientLng = $request->long ?? $user->long ?? null;

		// Calculate match scores and rank sellers
		$recommendationService = new SellerRecommendationService();
		$rankedUsers = $recommendationService->rankSellers($users, $clientLat, $clientLng);

		// Paginate the ranked results
		$perPage = $request->count ?? 12;
		$currentPage = $request->page ?? 1;
		$items = $rankedUsers->forPage($currentPage, $perPage);
		$paginatedUsers = new \Illuminate\Pagination\LengthAwarePaginator(
			$items,
			$rankedUsers->count(),
			$perPage,
			$currentPage,
			['path' => $request->url(), 'query' => $request->query()]
		);

		return view('app.pages.sellers', compact('paginatedUsers', 'request', 'roles', 'experiences', 'isAdmin', 'isClient', 'clientLat', 'clientLng'));
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
	public function show(User $user)
	{
		// Require authentication to view seller profiles
		if (!auth()->check()) {
			// Store intended URL only for GET requests to avoid CSRF issues
			if (request()->isMethod('GET')) {
				session()->put('url.intended', request()->fullUrl());
			}
			return redirect()->route('login')->with('info', 'Please log in to view seller profiles.');
		}

		$currentUser = auth()->user();
		$currentUserRole = $currentUser->role->slug ?? '';
		$isAdmin = $currentUser->role_id == 1 || $currentUserRole === 'admin';
		$isClient = $currentUserRole === 'client';
		$isServiceProvider = in_array($currentUserRole, ['carer', 'childminder', 'housekeeper']);

		// Check if the profile being viewed is a seller (carer/childminder/housekeeper)
		$viewedUserIsSeller = in_array($user->role_id ?? 0, [3, 4, 5]); // Carer, Childminder, Housekeeper role IDs

		// Only Clients and Admins can view seller profiles
		if ($viewedUserIsSeller && !$isAdmin && !$isClient) {
			$roleTitle = $currentUser->role->title ?? 'Service Provider';
			return redirect()->route('profile')
				->with('info', "As a {$roleTitle}, you can view your own profile. Only clients can view service provider profiles.");
		}

		// Service providers cannot view profiles of other service providers of the same role
		if ($isServiceProvider && !$isAdmin) {
			// If viewing another service provider's profile
			if ($viewedUserIsSeller && $user->role_id == $currentUser->role_id) {
				$roleTitle = strtolower($currentUser->role->title ?? 'service providers');
				return redirect()->route('profile')
					->with('info', "You cannot view profiles of other {$roleTitle}. Only clients can view service provider profiles.");
			}
		}

		$days = Day::all();
		$time_types = TimeType::all();
		return view('app.pages.profile.show', compact('user', 'days', 'time_types', 'isAdmin', 'isClient'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
	}
}
