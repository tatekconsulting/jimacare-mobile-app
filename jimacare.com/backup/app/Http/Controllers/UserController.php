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
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;

class UserController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$roles = Role::where('seller', true)->get();
		$experiences = Experience::all();
		$users = app(Pipeline::class)->send(User::query())->through([
			ActiveFilter::class,
			SellerTypeFilter::class,
			SellerExperienceFilter::class,
			SellerLocationFilter::class
		])->thenReturn()->paginate($request->count ?? 12);


		return view('app.pages.sellers', compact('users', 'request', 'roles', 'experiences'));
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
		$days = Day::all();
		$time_types = TimeType::all();
		return view('app.pages.profile.show', compact('user', 'days', 'time_types'));
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
