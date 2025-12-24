<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with('role')->get();
    	return view('admin.pages.post.index', compact('posts') );
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
        return view('admin.pages.post.create', compact('roles') );
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
		    'desc'      => 'bail|required|string|min:4',
		    'image'     => 'bail|required|image',
		    'banner'    => 'bail|required|image',
	    ]);

	    $data = array_merge( $request->only(['title', 'desc']), [
		    'slug'      => Str::slug($request->title),
            'role_id'   => $request->type,
		    'user_id'   => 1
	    ]);

	    if($request->hasFile('image')){
		    $path = $request->file('image')->store('public/image');
		    $path = 'storage/' . (explode('public/', $path)[1]);
		    $data['image'] = $path;
	    }

	    if($request->hasFile('banner')){
		    $path = $request->file('banner')->store('public/image');
		    $path = 'storage/' . (explode('public/', $path)[1]);
		    $data['banner'] = $path;
	    }

	    $post =  Post::create($data);

	    session()->flash('notice', 'Post has been updated!');
	    return redirect()->route('dashboard.post.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  Post $post
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Post $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Post $post)
    {
	    $roles = Role::where('seller', true)
		    ->where('active', true)
		    ->get()
	    ;
	    return view('admin.pages.post.edit', compact('roles', 'post') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Post $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
	    $request->validate([
		    'title'     => 'bail|required|string|min:4|max:255',
		    'type'      => 'bail|required|numeric|exists:roles,id',
		    'desc'      => 'bail|required|string|min:4',
		    'image'     => 'bail|sometimes|image',
		    'banner'    => 'bail|sometimes|image',
	    ]);

	    $data = array_merge( $request->only(['title', 'desc']), [
		    'slug'      => Str::slug($request->title),
		    'role_id'   => $request->type,
	    ]);

	    if($request->hasFile('image')){
		    $path = $request->file('image')->store('public/image');
		    $path = 'storage/' . (explode('public/', $path)[1]);
		    $data['image'] = $path;
	    }

	    if($request->hasFile('banner')){
		    $path = $request->file('banner')->store('public/image');
		    $path = 'storage/' . (explode('public/', $path)[1]);
		    $data['banner'] = $path;
	    }

	    $post->update($data);

	    session()->flash('notice', 'Post has been updated!');
	    return redirect()->route('dashboard.post.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Post $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('dashboard.post.index');
    }
}
