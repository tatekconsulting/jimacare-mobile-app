@extends('app.template.layout')

@section('content')
	<div class="blog-head mt-5">
		<div class="container my-5">
			<div class="row">
				<div class="col-md-12">
					<div class="row">

						<div class="col-12 col-md-7">
							<h3>Jimacare Blog</h3>
						</div>

						<div class="col-12 col-md-5">
							<form class="row">
								<div class="col-12 input-group mb-2">
									<input type="search" name="search" value="{{ request('search') }}"
										   id="search" class="form-control search"
										   placeholder="Search"
									/>
									<div class="input-group-append">
										<button class="btn btn-primary fa fa-search" type="submit"></button>
									</div>
								</div>
								<div class="col-12 text-right">
									@foreach($roles as $role)
										<label for="type_{{ $role->id }}" class="btn text-left btn-outline-primary" data-toggle="button"
											@if(in_array($role->id, request('type', []) )) aria-pressed="true" @else aria-pressed="false" @endif
										>
											<input type="checkbox" name="type[]" value="{{ $role->id }}"
												   id="type_{{ $role->id }}" class="type"
												   @if( in_array($role->id, request('type', [])) ) checked @endif
											/>{{ $role->title }}
										</label>
									@endforeach
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="blog-content">
		<div class="container">
			<div class="row">
				@if($posts->count() > 0)
					@foreach($posts ?? [] as $post)
						<div class="col-12 col-sm-6 col-md-4 mb-4">
							<div class="blog-post">
								<img src="{{ asset($post->image) }}" alt="">
								<div class="d-inline-block w-100 p-3">
									<h4 class="mb-2">{{ $post->title }}</h4>
									<p class="mb-2">{!! substr($post->desc, 0, 200) !!}</p>
									<a href="{{ route('post', ['post' => $post->id]) }}">Read More</a>
								</div>
							</div>
						</div>
					@endforeach
				@else
					<div class="col-12 py-5 text-center">
						<h4 class="py-5">No Result Found!</h4>
					</div>
				@endif
			</div>
			<div class="row">
				<div class="col-12">
					{{ $posts->links('app.template.pagination') }}
					{{--<a class="btn btn-danger" href="" >Next Page <span class="ml-3 fa fa-long-arrow-right"></span></a>--}}
				</div>
			</div>
		</div>
	</div>
@endsection
