<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use App\Models\Role;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	    $types = Type::with('role')->get();
	    return view('admin.pages.type.index', compact('types') );
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
	    return view('admin.pages.type.create', compact('roles') );
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

	    $type = Type::create($data);

	    session()->flash('notice', 'Type has been updated!');
	    return redirect()->route('dashboard.type.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function show(Type $type)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function edit(Type $type)
    {
	    $roles = Role::where('seller', true)
		    ->where('active', true)
		    ->get()
	    ;
	    return view('admin.pages.type.edit', compact('roles', 'type') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Type $type)
    {
	    $request->validate([
		    'title'     => 'bail|required|string|min:4|max:255',
		    'type'      => 'bail|required|numeric|exists:roles,id',
	    ]);

	    $data = array_merge( $request->only(['title']), [
		    'slug'      => Str::slug($request->title),
		    'role_id'   => $request->type,
	    ]);

	    $type->update($data);

	    session()->flash('notice', 'Type has been updated!');
	    return redirect()->route('dashboard.type.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function destroy(Type $type)
    {
	    $type->delete();
	    return redirect()->route('dashboard.type.index');
    }
}
