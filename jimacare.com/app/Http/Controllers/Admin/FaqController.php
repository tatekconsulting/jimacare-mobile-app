<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Post;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	    $faqs = Faq::with('role')->get();
	    return view('admin.pages.faq.index', compact('faqs') );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
	    $roles = Role::where('active', true)
		    ->get()
	    ;
	    return view('admin.pages.faq.create', compact('roles') );
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
		    'desc'      => 'bail|required|string|min:4'
	    ]);

	    $data = array_merge( $request->only(['title', 'desc']), [
		    'role_id'   => $request->type,
	    ]);
	    $faq =  Faq::create($data);

	    session()->flash('notice', 'Faq has been updated!');
	    return redirect()->route('dashboard.faq.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function show(Faq $faq)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function edit(Faq $faq)
    {
	    $roles = Role::where('active', true)
		    ->get()
	    ;
	    return view('admin.pages.faq.edit', compact('roles', 'faq') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Faq $faq)
    {
	    $request->validate([
		    'title'     => 'bail|required|string|min:4|max:255',
		    'type'      => 'bail|required|numeric|exists:roles,id',
		    'desc'      => 'bail|required|string|min:4'
	    ]);

	    $data = array_merge( $request->only(['title', 'desc']), [
		    'role_id'   => $request->type,
	    ]);

	    $faq->update($data);

	    session()->flash('notice', 'Faq has been updated!');
	    return redirect()->route('dashboard.faq.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function destroy(Faq $faq)
    {
	    $faq->delete();
	    return redirect()->route('dashboard.faq.index');
    }
}
