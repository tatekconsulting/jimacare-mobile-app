@if($profile->role_id > 2)
	<div class="form-group col-12 cb-must-select">
		<label>
			@if($profile->role_id == 3)
				Experience as a carer
			@elseif($profile->role_id == 4)
				Experience with Ages
			@elseif($profile->role_id == 5)
				Choose experience you have
			@endif
		</label>
		<div>
			@foreach($experiences as $exp)
				<label for="experience_{{ $exp->id }}" class="btn btn-outline-primary"
					   data-toggle="button"
					   @if(old('experience',in_array($exp->id, $profile->experiences->pluck('id')->toArray()))) aria-pressed="true"
					   @else aria-pressed="false" @endif
				>
					<input type="checkbox" name="experience[]" value="{{ $exp->id }}"
						   id="experience_{{ $exp->id }}" class="experience"
						   @if(old('experience',in_array($exp->id, $profile->experiences->pluck('id')->toArray()))) checked @endif
						{{-- @if( count($profile->experiences->pluck('id')) < 1 ) required @endif--}}
					/>{{ $exp->title }}
				</label>
			@endforeach
		</div>
	</div>
@endif
