@if($profile->role_id == 3)
	<div class="col-12 cb-must-select">
		<div class="row">
			<div class="col-12">Service Type</div>
		</div>
		@foreach($types as $type)
			@php
				$avail = $profile->availabilities
					->where('type_id', $type->id)
					->first()
				;
			@endphp
			<div class="row">
				<div class="col-4 col-md-4 form-group py-2">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" name="availability[{{$type->id}}][available]" value="1"
							   id="availability_{{$type->id}}_available" class="custom-control-input available"
							   @if($avail && $avail->available ) checked @endif
						/>
						<label class="custom-control-label" for="availability_{{$type->id}}_available">{{ ucfirst($type->title) }}</label>
					</div>
				</div>
				<div class="col-8 col-md-4 input-group mb-3 ml-auto">
					<input type="number" name="availability[{{ $type->id }}][charges]" class="form-control charges"
						   placeholder="{{ $type->name }} Charges"
						   @if($avail && $avail->charges ) value="{{ $avail->charges }}" @endif
					/>
					<div class="input-group-append">
						<span class="input-group-text">Per Hour</span>
					</div>
				</div>
			</div>
		@endforeach
	</div>
@endif
