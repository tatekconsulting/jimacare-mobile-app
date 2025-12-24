<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use App\Models\Post;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InterestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	    $interests = Interest::with('role')->get();
	    return view('admin.pages.interest.index', compact('interests') );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
	    $roles = Role::where('seller', true)
		    ->where('active', true)
		    ->get()
	    ;
	    return view('admin.pages.interest.create', compact('roles') );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	    $request->validate([
		    'title'     => 'bail|required|string|min:4|max:255',
		    'type'      => 'bail|required|numeric|exists:roles,id'
	    ]);

	    $data = array_merge( $request->only(['title']), [
		    'slug'      => Str::slug($request->title),
		    'role_id'   => $request->type,
	    ]);

	    $interest = Interest::create($data);

	    session()->flash('notice', 'Interest has been updated!');
	    return redirect()->route('dashboard.interest.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Interest  $interest
     * @return \Illuminate\Http\Response
     */
    public function show(Interest $interest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Interest  $interest
     * @return \Illuminate\Http\Response
     */
    public function edit(Interest $interest)
    {
	    $roles = Role::where('seller', true)
		    ->where('active', true)
		    ->get()
	    ;
	    return view('admin.pages.interest.edit', compact('roles', 'interest') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Interest  $interest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Interest $interest)
    {
	    $request->validate([
		    'title'     => 'bail|required|string|min:4|max:255',
		    'type'      => 'bail|required|numeric|exists:roles,id',
	    ]);

	    $data = array_merge( $request->only(['title']), [
		    'slug'      => Str::slug($request->title),
		    'role_id'   => $request->type,
	    ]);

	    $interest->update($data);

	    session()->flash('notice', 'Interest has been updated!');
	    return redirect()->route('dashboard.interest.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Interest  $interest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Interest $interest)
    {
	    $interest->delete();
	    return redirect()->route('dashboard.interest.index');
    }
}
