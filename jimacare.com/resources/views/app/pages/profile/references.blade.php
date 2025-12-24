@if(!in_array($profile->role_id,[1,2]))
	@for($i = 1; $i<3; $i++)
		<div class="col-12 mt-3">
			<h5>{{ $profile->role->title }} Reference {{ $i }} 	@if (Request::is('profile'))

	    	@if(true == old("referee".$i."_status",$profile->{'referee' . $i . '_status'})) 
	    <div class="badge badge-success" style="font-size: 14px; margin-left: 5px;"> Approved
	    </div>
	    @else
	     <div class="badge badge-warning" style="font-size: 14px; margin-left: 5px;"> Pending
	    </div>
	    
	    @endif
	
			@endif </h5> 
		</div>
@if (Request::is('dashboard/user/*'))
		<div class="col-6 mt-3">
			<label for="referee{{ $i }}_status">Reference {{ $i }} status</label>
			<select name="referee{{ $i }}_status"
					id="referee{{ $i }}_status" class="referee{{ $i }}_status custom-select" required
			>
					<option value="true"
							@if(true == old("referee".$i."_status",$profile->{'referee' . $i . '_status'})) selected @endif
					>Active</option>
					<option value="false"
							@if(false == old("referee".$i."_status",$profile->{'referee' . $i . '_status'})) selected @endif
					>Inactive</option>

			</select>
		</div>
		<div class="col-6 mt-3">
		</div>
	@endif

		<div class="form-group col-12 col-md-6">
			<label for="referee{{ $i }}_name">Referee's Name</label>
			<input type="text" name="referee{{ $i }}_name" value="{{ old("referee".$i."_name",$profile->{'referee' . $i . '_name'} ?? '') }}"
				   id="referee{{ $i }}_name" class="referee{{ $i }}_name form-control" placeholder="Referee's Name" required
			/>
		</div>

		<div class="form-group col-12 col-md-6">
			<label for="referee{{ $i }}_email">Referee's Email</label>
			<input type="text" name="referee{{ $i }}_email" value="{{ old("referee".$i."_email",$profile->{'referee' . $i . '_email'} ?? '') }}"
				   id="referee{{ $i }}_email" class="referee_email form-control" placeholder="Referee's Email" required
			/>
		</div>

		<div class="form-group col-12 col-md-6">
			<label for="referee{{ $i }}_phone">Referee's Phone Number </label>
			<input type="text" name="referee{{ $i }}_phone" value="{{ old("referee".$i."_phone",$profile->{'referee' . $i . '_phone'} ?? '') }}"
				   id="referee{{ $i }}_phone" class="referee{{ $i }}_phone form-control" placeholder="Referee's Phone" required
			/>
		</div>

		<div class="form-group col-12 col-md-6">
			<label for="referee{{ $i }}_country">Country </label>
			<select name="referee{{ $i }}_country_id"
					id="referee{{ $i }}_country" class="referee{{ $i }}_country custom-select" required
			>
				<option value="">Country</option>
				@foreach($countries as $country)
					<option value="{{ $country->id }}"
							@if($country->id == old("referee".$i."_country",$profile->{'referee' . $i . '_country_id'})) selected @endif
					>{{ ucfirst($country->title) }}</option>
				@endforeach
			</select>
		</div>

		@if($profile->role_id == 4)
			<div class="form-group col-12">
				<label for="referee{{ $i }}_child_age">Age of children in your care</label>
				<input type="text" name="referee{{ $i }}_child_age" value="{{ old("referee".$i."_child_age",$profile->{'referee' . $i . '_child_age'} ?? '') }}"
					   id="referee{{ $i }}_child_age" class="referee{{ $i }}_child_age form-control" placeholder="Age of children in your care" required
				/>
			</div>
		@endif

		<div class="form-group col-12">
			<label for="referee{{ $i }}_how_long">How long have they known you?</label>
			<input type="text" name="referee{{ $i }}_how_long" value="{{ old("referee".$i."_how_long",$profile->{'referee' . $i . '_how_long'} ?? '') }}"
				   id="referee{{ $i }}_how_long" class="referee{{ $i }}_how_long form-control" placeholder="How long have they known you?" required
			/>
		</div>

		<div class="form-group col-12">
			<label for="referee{{ $i }}_how_contact">How did you come in contact with them?</label>
			<textarea name="referee{{ $i }}_how_contact"
					  id="referee{{ $i }}_how_contact" class="referee{{ $i }}_how_contact form-control" placeholder="How did you come in contact with them?"
					  required
			>{{ old("referee".$i."_how_contact",$profile->{'referee' . $i . '_how_contact'} ?? '') }}</textarea>
		</div>

	@endfor
@endif
