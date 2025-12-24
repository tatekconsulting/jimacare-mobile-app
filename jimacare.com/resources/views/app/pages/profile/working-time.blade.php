@if($profile->role_id == 4)
	<div class="col-12">
		<label>Working Time</label>
		<div class="table-responsive mb-3">
			<table class="table table-borderless mb-0 cb-must-select">
				<thead>
				<tr>
					<td></td>
					@foreach($days as $day)
						<td>{{ $day->title }}</td>
					@endforeach
				</tr>
				</thead>
				<tbody>
				@foreach($time_types ?? [] as $time)
					<tr>
						<td>{{ $time->title }}</td>
						@foreach($days as $day)
							@php $avail = $profile->time_availables->where('type_id', $time->id)->where('day_id', $day->id)->count(); @endphp
							<td class="text-center">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="availability[{{$day->id}}][]" value="{{$time->id}}"
										   id="availability_{{ $day->slug }}_{{ $time->slug }}" class="custom-control-input available"
										   @if( $avail > 0 ) checked @endif
									/>
									<label class="custom-control-label" for="availability_{{$day->slug}}_{{ $time->slug }}"></label>
								</div>
							</td>
						@endforeach
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	</div>
@endif
