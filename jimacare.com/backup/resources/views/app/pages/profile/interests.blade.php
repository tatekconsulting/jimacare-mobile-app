@if($profile->role_id == 4)
	<div class="form-group col-12 cb-must-select">
		<label>Interest</label>
		<div>
			@foreach($interests as $interest)
				<label for="interest_{{ $interest->id }}"
					   class="btn btn-outline-primary" data-toggle="button"
					   @if(old('interest',in_array($interest->id, $profile->interests->pluck('id')->toArray()))) aria-pressed="true"
					   @else aria-pressed="false" @endif
				>
					<input type="checkbox" name="interest[]" value="{{ $interest->id }}"
						   id="interest_{{ $interest->id }}" class="interest"
						   @if(old('interest',in_array($interest->id, $profile->interests->pluck('id')->toArray()))) checked @endif
						   @if( count($profile->interests->pluck('id')) < 1 ) required @endif
					/> {{ $interest->title }}
				</label>
			@endforeach
		</div>
	</div>
@endif
