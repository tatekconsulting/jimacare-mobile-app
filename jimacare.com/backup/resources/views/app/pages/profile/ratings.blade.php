@extends('app.template.layout-profile')

@section('content')
	<div class="about-you">
		<div class="step-wrap">
			<form method="POST" action="{{ route('ratings') }}" enctype="multipart/form-data" id="about1" class="row about-form novalidate">
				@csrf
				<div class="col-12 pb-3">
					<h3 class="bg-white text-dark p-3">My Ratings</h3>
				</div>


				<div class="col-12 pb-4">
					@foreach($reviews ?? [] as $review)
						<div class="row pb-5 px-3">
							<div class="col-12">
								<div class="row no-gutters">
									{{--<div class="col-9">
										<h5 class="text-primary font-weight-bold">{{ $review->title ?? '' }}</h5>
									</div>--}}
									<div class="col-12">
										<span class="rating raty readable" data-score="{{ $review->stars ?? 0}}"></span>
									</div>
								</div>
							</div>
							<div class="col-12 py-2">
								{{ $review->desc ?? '' }}
							</div>
							<div class="col-12">
								By {{ $review->client->firstname }} on {{ $review->created_at->format('d/m/Y') }}
							</div>
						</div>
					@endforeach
				</div>



			</form>
		</div>
	</div>
@endsection
