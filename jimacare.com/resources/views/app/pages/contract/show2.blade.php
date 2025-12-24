@extends('app.template.layout')

@section('content')
	<div class="pt-5">
		<div class="container">
			<div class="row">
				<div class="col-12 py-4 border-bottom">
					<div class="row">
						<div class="seller-left text-center col-12 col-md-auto">
							<div class="seller-img">
								<img class="img img-fluid rounded-circle seller-avatar" src="{{ asset($contract->user->profile ?? 'img/undraw_profile.svg') }}"
									 alt="{{ $contract->user->firstname }} {{ $contract->user->lastname }}">
							</div>
							<div class="mb-1">
								<span class="fa fa-lg fa-id-card-o text-primary mx-1"></span>
								<span class="fa fa-lg fa-file text-primary mx-1"></span>
								<span class="fa fa-lg fa-shield text-primary mx-1"></span>
							</div>
							{{--<a class="btn btn-light">£ {{ number_format($contract->user->fee, 2) }}/hour</a>--}}
						</div>
						<div class="col-12 col-md seller-right">

							<h3>{{ $contract->title ?? '' }} in <small>{{ $contract->user->country->title ?? '' }}</small></h3>
							<div class="row">
								<div class="col-12">
									@if($contract->user->gender ?? false)
										<span class="btn btn-sm btn-primary disabled">{{ ucfirst($contract->user->gender) }}</span>
									@endif
									<span class="btn btn-sm btn-primary disabled">Member Since {{ $contract->user->created_at->format('M Y') ?? '' }}</span>
									<span class="btn btn-sm btn-primary disabled">Last Updated {{ $contract->user->updated_at->format('M Y') ?? '' }}</span>
								</div>
								<div class="col-12 mt-2">
									<a href="#" class="btn btn-primary px-4"><span class="fa fa-envelope mr-2"></span>Message</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 pt-3">
					<div class="row">
						@if($contract->desc ?? false)
							<div class="col-12">
								<h3>Information</h3>
								<p>{{ $contract->desc }}</p>
							</div>
						@endif

						<div class="col-12 col-md-6">
							<h4>When to join</h4>
							<p>
								@if($contract->start_type == 'immediately')
									Immediately
								@elseif($contract->start_type == 'not-sure')
									Not Sure
								@else
									{{ $contract->start_date->format('d M Y') }}
								@endif
							</p>
						</div>

						<div class="col-12 col-md-6">
							<h4>When to end</h4>
							<p>
								@if($contract->end_type == 'on-going')
									On Going
								@else
									{{ $contract->end_date->format('d M Y') }}
								@endif
							</p>
						</div>

						<div class="col-12 col-md-6">
							<h4>Time of Arrival</h4>
							<p>{{ $contract->start_time->format('H:i') }}</p>
						</div>

						<div class="col-12 col-md-6">
							<h4>Time of Leave</h4>
							<p>{{ $contract->end_time->format('H:i') }}</p>
						</div>

						@if($contract->role->id == 4)
							<div class="col-12 col-md-12">
								<h4>Required to Drive?</h4>
								<p>{{ $contract->drive ? 'Yes' : 'No' }}</p>
							</div>
						@endif

						@if($contract->role->id == 3)
							<div class="col-12 pt-3">
								<h3>Services Type</h3>
								<ul class="mb-3">
									@foreach($contract->types as $type)
										<li><span class="fa fa-lg fa-check mr-2 text-success"></span> {{ $type->title }}</li>
									@endforeach
								</ul>
							</div>
						@endif

						@if($contract->role->id == 5)
							<div class="col-12 col-md-6">
								<h4>How often do you need cleaning?</h4>
								<p>{{ $contract->how_often ?? '' }}</p>
							</div>
							<div class="col-12 col-md-6">
								<h4>How many bedroom(s) need cleaning?</h4>
								<p>{{ $contract->beds ?? '' }}</p>
							</div>
							<div class="col-12 col-md-6">
								<h4>How many bathroom(s) need cleaning?</h4>
								<p>{{ $contract->baths ?? '' }}</p>
							</div>
							<div class="col-12 col-md-6">
								<h4>How many reception room(s) need cleaning?</h4>
								<p>{{ $contract->rooms ?? '' }}</p>
							</div>
							<div class="col-12 col-md-6">
								<h4>What type of cleaning would you like?</h4>
								<p>{{ $contract->cleaning_type ?? '' }}</p>
							</div>
						@endif

						@if(in_array($contract->role_id, [3,5]))
							<div class="col-12">
								<h3>Working Days</h3>
								<ul class="mb-3">
									@foreach($contract->days as $day)
										<li><span class="fa fa-lg fa-check mr-2 text-success"></span> {{ $day->title }}</li>
									@endforeach
								</ul>
							</div>
						@endif

						@if($contract->role_id == 4 )
							<div class="col-12">
								<h3>Working Time</h3>
								<div class="table-responsive mb-3">
									<table class="table table-bordered table-striped  mb-0">
										<thead>
										<tr>
											<td></td>
											@foreach($days as $day)
												<td class="text-center">{{ $day->title }}</td>
											@endforeach
										</tr>
										</thead>
										<tbody>
										@foreach($time_types ?? [] as $time)
											<tr>
												<td>{{ $time->title }}</td>
												@foreach($days as $day)
													@php $avail = $contract->time_availables->where('type_id', $time->id)->where('day_id', $day->id)->count(); @endphp
													<td class="text-center">
														<span class="fa fa-lg fa-{{ ($avail > 0) ? 'check-square-o text-success' : 'times-circle text-danger' }}"></span>
													</td>
												@endforeach
											</tr>
										@endforeach
										</tbody>
									</table>
								</div>
							</div>
						@endif

						<div class="col-12">
							<h3>Languages</h3>
							<ul class="mb-3">
								@foreach($contract->languages as $language)
									<li><span class="fa fa-lg fa-check mr-2 text-success"></span> {{ $language->title }}</li>
								@endforeach
							</ul>
						</div>

						<div class="col-12">
							<h3>Experiences Required</h3>
							<ul class="mb-3">
								@foreach($contract->experiences as $exp)
									<li><span class="fa fa-lg fa-check mr-2 text-success"></span> {{ $exp->title }}</li>
								@endforeach
							</ul>
						</div>
						@if($contract->role_id == 4)
							<div class="col-12">
								<h3>Interests</h3>
								<ul class="mb-3">
									@foreach($contract->interests as $interest)
										<li><span class="fa fa-lg fa-check mr-2 text-success"></span> {{ $interest->title }}</li>
									@endforeach
								</ul>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
