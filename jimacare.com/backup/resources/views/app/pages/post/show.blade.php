@extends('app.template.layout')

@section('content')
	<div class="about-banner">
		<div class="container">
			<div class="row">
				<div class="col-md-7">
					<h3 class="mt-3">{{ $post->title }}</h3>
				</div>
				<div class="col-12">
					<div class="banner-img">
						<img class="img-responsive" src="{{ asset($post->banner ?? 'img/aboutbanner.png') }}" alt="">
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="blogdetail-content">
		<div class="container">
			<div class="row mt-5 pb-4">
				<div class="col-md-12 col-sm-12 col-12">
					<p>{!! $post->desc !!}</p>
				</div>
			</div>
		</div>
	</div>
@endsection
