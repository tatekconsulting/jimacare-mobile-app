<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Models\Role;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	    $skills = Skill::with('role')->get();
	    return view('admin.pages.skill.index', compact('skills') );
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
	    return view('admin.pages.skill.create', compact('roles') );
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
		    'type'      => 'bail|required|numeric|exists:roles,id',
	    ]);

	    $data = array_merge( $request->only(['title']), [
		    'slug'      => Str::slug($request->title),
		    'role_id'   => $request->type,
	    ]);

	    $skill =  Skill::create($data);

	    session()->flash('notice', 'Skill has been updated!');
	    return redirect()->route('dashboard.skill.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Skill  $skill
     * @return \Illuminate\Http\Response
     */
    public function show(Skill $skill)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Skill  $skill
     * @return \Illuminate\Http\Response
     */
    public function edit(Skill $skill)
    {
	    $roles = Role::where('seller', true)
		    ->where('active', true)
		    ->get()
	    ;
	    return view('admin.pages.skill.edit', compact('roles', 'skill') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Skill  $skill
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Skill $skill)
    {
	    $request->validate([
		    'title'     => 'bail|required|string|min:4|max:255',
		    'type'      => 'bail|required|numeric|exists:roles,id',
	    ]);

	    $data = array_merge( $request->only(['title']), [
		    'slug'      => Str::slug($request->title),
		    'role_id'   => $request->type
	    ]);

	    $skill->update($data);

	    session()->flash('notice', 'Skill has been updated!');
	    return redirect()->route('dashboard.skill.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Skill  $skill
     * @return \Illuminate\Http\Response
     */
    public function destroy(Skill $skill)
    {
	    $skill->delete();
	    return redirect()->route('dashboard.skill.index');
    }
}
