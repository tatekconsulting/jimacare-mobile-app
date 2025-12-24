@if($role->id > 2)
	<div class="form-group col-12 cb-must-select">
		<label>
			@if($role->id == 3)
				Experience as a carer
			@elseif($role->id == 4)
				Experience with Ages
			@elseif($role->id == 5)
				Choose experience must have
			@endif
		</label>
		<div>
			@foreach($experiences as $exp)
				<label for="experience_{{ $exp->id }}" class="btn btn-outline-primary"
					   data-toggle="button"
					   @if(in_array($exp->id, old('experience', []) )) aria-pressed="true"
					   @else aria-pressed="false" @endif
				>
					<input type="checkbox" name="experience[]" value="{{ $exp->id }}"
						   id="experience_{{ $exp->id }}" class="experience"
						   @if(in_array($exp->id, old('experience', []) )) checked @endif
						   @if( count(old('experience', [])) < 1 ) required @endif
					/>{{ $exp->title }}
				</label>
			@endforeach
		</div>
	</div>
@endif
