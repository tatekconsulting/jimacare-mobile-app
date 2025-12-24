@if(in_array($role->id, [3, 5]) )
	<div class="form-group col-12 cb-must-select">
		<label>
			@if($role->id == 3)
				Working Day
			@else
				What are the best days for cleaning?
			@endif
		</label>
		<div>
			@foreach($days as $day)
				<label class="btn btn-outline-primary" data-toggle="button"
					   @if(in_array($day->id, old('day', []) )) aria-pressed="true"
					   @else aria-pressed="false" @endif
				>
					<input type="checkbox" name="day[]" value="{{ $day->id }}"
						   id="day_{{ $day->id }}"
						   class="day"
						   @if(in_array($day->id, old('day', []) )) checked @endif
						   @if( count( old('day', [])) < 1 ) required @endif
					/> {{ $day->title }}
				</label>
			@endforeach
		</div>
	</div>
@endif
