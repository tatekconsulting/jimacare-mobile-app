@if($role->id == 4)
	<div class="form-group col-12 cb-must-select">
		<label>General Education (not Childcare related)</label>
		<div>
			@foreach($educations as $education)
				<label for="education_{{ $education->id }}"
					   class="btn btn-outline-primary" data-toggle="button"
					   @if( in_array($education->id, $contract->educations->pluck('id')->toArray()) ) aria-pressed="true"
					   @else aria-pressed="false" @endif
				>
					<input type="checkbox" name="education[]" value="{{ $education->id }}"
						   id="education_{{ $education->id }}" class="education"
						   @if( in_array($education->id, $contract->educations->pluck('id')->toArray()) ) checked @endif
						   @if( count( $contract->educations->pluck('id')->toArray() ) < 1 ) required @endif
					/> {{ $education->title }}
				</label>
			@endforeach
		</div>
	</div>
@endif
