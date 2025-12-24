@if($profile->role_id == 3 )
	<div class="form-group col-12 cb-must-select">
		<label>Working Day</label>
		<div>
			@foreach(\App\Models\Day::all() as $day)
				<label class="btn btn-outline-primary" data-toggle="button"
					   @if(old('day',in_array($day->id, $profile->days->pluck('id')->toArray()))) aria-pressed="true"
					   @else aria-pressed="false" @endif
				>
					<input type="checkbox" name="day[]" value="{{ $day->id }}"
						   id="day_{{ $day->id }}" class="day"
						   @if(old('day',in_array($day->id, $profile->days->pluck('id')->toArray()))) checked @endif
						   @if( count($profile->days->pluck('id')) < 1 ) required @endif
					/> {{ $day->title }}
				</label>
			@endforeach
		</div>
	</div>
@endif
