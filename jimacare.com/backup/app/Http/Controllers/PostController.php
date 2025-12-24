<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\Role;
use App\QueryFilter\InTypeFilter;
use App\QueryFilter\PostSearchFilter;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	    $roles = Role::where('seller', true)
		    ->where('active', true)
		    ->get()
	    ;

	    $posts = app(Pipeline::class)->send(Post::query())->through([
	    	PostSearchFilter::class,
	    	InTypeFilter::class,
	    ])->thenReturn()->paginate(4)->withQueryString();

        return view('app.pages.post.index', compact('roles', 'posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('app.posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $data = [
        	'category_id'   => $request->category,
        	'title'         => $request->title,
	        'desc'          => $request->desc
        ];

	    if($request->hasFile('image')){
		    $path = $request->file('image')->store('public/image');
		    $path = 'storage/' . (explode('public/', $path)[1]);
		    $data['image'] = $path;
	    }
	    $data['user_id'] = auth()->id();

	    if($request->type == 'day')

	    $post = Post::create($data);
	    $post->tags()->sync($request->tag);
	    session()->flash('status', 'Post has been published!');
	    return redirect('post.edit', [ 'post' => $post->slug ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
    	return view('app.pages.post.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return view('app.post.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, Post $post)
    {
	    $data = [
		    'category_id'   => $request->category,
		    'title'         => $request->title,
		    'desc'          => $request->desc
	    ];

	    if($request->hasFile('image')){
		    $path = $request->file('image')->store('public/image');
		    $path = 'storage/' . (explode('public/', $path)[1]);
		    $data['image'] = $path;
	    }
	    $data['user_id'] = auth()->id();

	    $post->update($data);
	    $post->tags()->sync($request->tag);
	    session()->flash('status', 'Post has been published!');
	    return redirect()->route('post.edit', [ 'post' => $post->slug ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
    	$post->delete();
	    session()->flash('status', 'Post has been published!');
    	return redirect()->route('post.index');
    }
}
