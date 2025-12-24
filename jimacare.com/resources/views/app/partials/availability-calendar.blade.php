{{-- Availability Calendar Component --}}
{{-- Usage: @include('app.partials.availability-calendar', ['user' => $carer, 'days' => $days, 'time_types' => $time_types]) --}}

@if(isset($user) && $user)
	<div class="availability-calendar mb-3">
		<h5 class="mb-2">
			<i class="fa fa-calendar"></i> Availability
			@if($user->available_now)
				<span class="badge badge-success ml-2">
					<i class="fa fa-circle"></i> Available Now
					@if($user->available_until)
						(until {{ \Carbon\Carbon::parse($user->available_until)->format('H:i') }})
					@endif
				</span>
			@else
				<span class="badge badge-secondary ml-2">Not Available Now</span>
			@endif
		</h5>
		
		@if($user->days && $user->days->count() > 0)
			<div class="mb-2">
				<strong>Working Days:</strong>
				@foreach($user->days as $day)
					<span class="badge badge-primary mr-1">{{ $day->title }}</span>
				@endforeach
			</div>
		@endif
		
		@if($user->time_availables && $user->time_availables->count() > 0 && isset($time_types))
			<div class="table-responsive">
				<table class="table table-sm table-bordered">
					<thead>
						<tr>
							<th>Time</th>
							@if(isset($days))
								@foreach($days as $day)
									<th class="text-center">{{ $day->title }}</th>
								@endforeach
							@endif
						</tr>
					</thead>
					<tbody>
						@foreach($time_types as $time)
							<tr>
								<td><strong>{{ $time->title }}</strong></td>
								@if(isset($days))
									@foreach($days as $day)
										@php
											$avail = $user->time_availables
												->where('type_id', $time->id)
												->where('day_id', $day->id)
												->count();
										@endphp
										<td class="text-center">
											@if($avail > 0)
												<span class="fa fa-check-circle text-success" title="Available"></span>
											@else
												<span class="fa fa-times-circle text-danger" title="Not Available"></span>
											@endif
										</td>
									@endforeach
								@endif
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		@elseif($user->days && $user->days->count() > 0)
			<p class="text-muted"><small>Available on selected days. Contact for specific times.</small></p>
		@else
			<p class="text-muted"><small>Availability information not provided.</small></p>
		@endif
	</div>
@endif

