@if($profile->role_id > 2 )
	<div class="form-group col-12 cb-must-select">
		<label>Skills</label>
		<div>
			@foreach($skills as $skill)
				<label for="skill_{{ $skill->id }}" class="btn btn-outline-primary" data-toggle="button"
					   @if(in_array($skill->id, old('skill',$profile->skills->pluck('id')->toArray()) )) aria-pressed="true" @else aria-pressed="false" @endif
				>
					<input type="checkbox" name="skill[]" value="{{ $skill->id }}"
						   id="skill_{{ $skill->id }}" class="skill"
						   @if(in_array($skill->id, old('skill',$profile->skills->pluck('id')->toArray()) )) checked @endif
						   @if( count($profile->skills->pluck('id')) < 1 ) required @endif
					/>{{ $skill->title }}
				</label>
			@endforeach
		</div>
	</div>
@endif
