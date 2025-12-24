@if($role->id == 3)
	<div class="form-group col-12 cb-must-select">
		<label>Service Type</label>
		<div>
			@foreach($types as $type)
				<label for="type_{{ $type->id }}" class="btn btn-outline-primary"
					   data-toggle="button"
					   @if(in_array($type->id, old('type', []) )) aria-pressed="true"
					   @else aria-pressed="false" @endif
				>
					<input type="checkbox" name="type[]" value="{{ $type->id }}"
						   id="type_{{ $type->id }}" class="type"
						   @if(in_array($type->id, old('type', []) )) checked @endif
						   @if( count(old('type', [])) < 1 ) required @endif
					/>{{ $type->title }}
				</label>
			@endforeach
		</div>
	</div>
@endif
