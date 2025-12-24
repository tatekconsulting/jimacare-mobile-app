@if($role->id == 4)
	<div class="form-group col-12 cb-must-select">
		<label>Interest</label>
		<div>
			@php $inters = $contract->interests->pluck('id')->toArray(); @endphp
			@foreach($interests as $interest)
				<label for="interest_{{ $interest->id }}"
					   class="btn btn-outline-primary" data-toggle="button"
					   @if( in_array($interest->id, $inters) ) aria-pressed="true"
					   @else aria-pressed="false" @endif
				>
					<input type="checkbox" name="interest[]" value="{{ $interest->id }}"
						   id="interest_{{ $interest->id }}" class="interest"
						   @if(in_array($interest->id, $inters )) checked @endif
						   @if( count($inters) < 1 ) required @endif
					/> {{ $interest->title }}
				</label>
			@endforeach
		</div>
	</div>
@endif
