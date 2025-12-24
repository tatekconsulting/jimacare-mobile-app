<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Education;
use App\Models\Interest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EducationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	    $educations = Education::with('role')->get();
	    return view('admin.pages.education.index', compact('educations') );
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
	    return view('admin.pages.education.create', compact('roles') );
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

	    $education = Education::create($data);

	    session()->flash('notice', 'Education has been updated!');
	    return redirect()->route('dashboard.education.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Education  $education
     * @return \Illuminate\Http\Response
     */
    public function show(Education $education)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Education  $education
     * @return \Illuminate\Http\Response
     */
    public function edit(Education $education)
    {
	    $roles = Role::where('seller', true)
		    ->where('active', true)
		    ->get()
	    ;
	    return view('admin.pages.education.edit', compact('roles', 'education') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Education  $education
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Education $education)
    {
	    $request->validate([
		    'title'     => 'bail|required|string|min:4|max:255',
		    'type'      => 'bail|required|numeric|exists:roles,id',
	    ]);

	    $data = array_merge( $request->only(['title']), [
		    'slug'      => Str::slug($request->title),
		    'role_id'   => $request->type,
	    ]);

	    $education->update($data);

	    session()->flash('notice', 'Education has been updated!');
	    return redirect()->route('dashboard.education.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Education  $education
     * @return \Illuminate\Http\Response
     */
    public function destroy(Education $education)
    {
	    $education->delete();
	    return redirect()->route('dashboard.education.index');
    }
}
