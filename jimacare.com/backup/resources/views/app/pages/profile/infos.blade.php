@if($profile->role_id > 2)
	<div class="form-group col-12">
		@php
			if($profile->role_id == 3){
				$info_label = 'Describe your care experience';
			}elseif($profile->role_id == 4){
				$info_label = 'Brief outline of professional childcare experience for at least the past 2 years';
			}elseif($profile->role_id == 5){
				$info_label = 'Describe your cleaning experience';
			}
		@endphp
		<label for="info">{{ $info_label }}</label>
		<textarea name="info" rows="4"
				  id="info" class="form-control info"
				  placeholder="{{ $info_label }}" required
		>{{ old('info',$profile->info) }}</textarea>
	</div>

	<div class="form-group col-12">
		@php
			if($profile->role_id == 3){
				$other_label = 'Tell us what makes you a Great Carer';
			}elseif($profile->role_id == 4){
				$other_label = 'Other information to support application';
			}elseif($profile->role_id == 5){
				$other_label = 'Why would you like to be Housekeeper?';
			}
		@endphp
		<label for="other">{{ $other_label }}</label>
		<textarea name="other" rows="4"
				  id="other" class="form-control other"
				  placeholder="{{ $other_label }}"
		>{{ old('other',$profile->other) }}</textarea>
	</div>
@endif
