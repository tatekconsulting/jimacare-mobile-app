<div class="form-group col-12 col-md-6">
	<label for="firstname">First Name</label>
	<input type="text" name="firstname" value="{{ old('firstname',$profile->firstname ?? '') }}"
		   id="firstname" class="firstname form-control"
		   placeholder="First Name" min="4" required
	/>
</div>
<div class="form-group col-12 col-md-6">
	<label for="lastname">Last Name</label>
	<input type="text" name="lastname" value="{{ old('lastname',$profile->lastname ?? '') }}"
		   id="lastname" class="lastname form-control"
		   placeholder="Last Name" min="4" required
	/>
</div>

<div class="form-group col-12 col-md-6">
	<label for="email">Email</label>
	<input type="email" name="email" value="{{ old('email',$profile->email ?? '') }}"
		   id="email" class="email form-control"
		   placeholder="Email Address" min="4" required
	/>
</div>
<div class="form-group col-12 col-md-6">
	<label for="phone">Phone Number </label>
	<input type="tel" name="phone" value="{{ old('phone',$profile->phone ?? '') }}"
		   id="phone" class="phone form-control"
		   placeholder="Phone Number" min="4" required
	/>
</div>
@if($profile->role_id !==2)
	<div class="form-group col-12">
		<label for="dob">Date of Birth </label>
		<input type="date" name="dob" value="{{ old('dob',$profile->dob ?? '') }}"
			   id="dob" class="dob form-control"
			   placeholder="Date of Birth" required
		/>
	</div>
@endif

<div class="form-group col-12">
	Gender<br/>
	<div class="custom-control custom-radio custom-control-inline">
		<input type="radio" name="gender" value="male"
			   id="male" class="gender custom-control-input"
			   @if(old('gender',$profile->gender) == 'male') checked @endif required
		/>
		<label class="custom-control-label" for="male">Male</label>
	</div>
	<div class="custom-control custom-radio custom-control-inline">
		<input type="radio" name="gender" value="female"
			   id="female" class="gender custom-control-input"
			   @if(old('gender',$profile->gender) == 'female') checked @endif required
		/>
		<label class="custom-control-label" for="female">Female</label>
	</div>
</div>
<div class="col-12 location-autofill">
	<div class="row">
		<div class="form-group col-md-4">
			<label for="address">Address</label>
			<input name="address" value="{{ old('address',$profile->address ?? '') }}" id="address" class="address form-control" placeholder="Address"
				   row="4"
				   required/>
		</div>
		@if(auth()->user()->role_id===1)
			<div class="form-group col-md-4">
				<label for="address">Lat</label>
				<input type="text" name="lat" class="form-control lat" value="{{ old('lat',$profile->lat ?? '') }}" required>
			</div>
			<div class="form-group col-md-4">
				<label for="address">Long</label>
				<input type="text" name="long" class="form-control long" value="{{ old('long',$profile->long ?? '') }}" required>
			</div>
		@else
			<input type="hidden" name="lat" class="lat" value="{{ old('lat',$profile->lat ?? '') }}">
			<input type="hidden" name="long" class="long" value="{{ old('long',$profile->long ?? '') }}">
		@endif

		<div class="form-group col-12 col-md-4">
			<label for="country">Country </label>
			<input type="text" name="country" value="{{ old('country',$profile->country ?? '') }}"
				   id="country" class="country form-control"
				   placeholder="Country" required
			/>
		</div>

		<div class="form-group col-12 col-md-4">
			<label for="city">City</label>
			<input type="text" name="city" value="{{ old('city',$profile->city ?? '') }}"
				   id="city" class="city form-control"
				   placeholder="City" required
			/>
		</div>

		<div class="form-group col-12 col-md-4">
			<label for="postcode">Postcode</label>
			<input type="text" name="postcode" value="{{ old('postcode',$profile->postcode ?? '') }}"
				   id="postcode" class="postcode form-control"
				   placeholder="Postcode" required
			/>
		</div>
	</div>
</div>
