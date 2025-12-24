@if($role->id == 3)
	<div class="form-group col-12 cb-must-select">
		<label>Service Type</label>
		<div>
			@php $typies = $contract->types->pluck('id')->toArray(); @endphp
			@foreach($types as $type)
				<label for="type_{{ $type->id }}" class="btn btn-outline-primary"
					   data-toggle="button"
					   @if(in_array($type->id, $typies )) aria-pressed="true"
					   @else aria-pressed="false" @endif
				>
					<input type="checkbox" name="type[]" value="{{ $type->id }}"
						   id="type_{{ $type->id }}" class="type"
						   @if(in_array($type->id, $typies)) checked @endif
						   @if( count($typies) < 1 ) required @endif
					/>{{ $type->title }}
				</label>
			@endforeach
		</div>
	</div>
@endif
