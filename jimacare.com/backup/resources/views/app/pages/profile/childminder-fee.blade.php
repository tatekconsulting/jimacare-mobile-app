@if($profile->role_id == 4)

	<div class="col-12 col-md-8">
		<label for="">Childminder Fee</label>
		<div class="input-group mb-3">
			<input type="number" name="fee" value="{{ old('fee',$profile->fee) ?? '' }}"
				   class="form-control fee"
				   placeholder="Childminder Fee"
				   min="1" step="1" required
			/>
			<div class="input-group-append">
				<span class="input-group-text">Per Hour</span>
			</div>
		</div>
	</div>

	<div class="col-12 col-md-4 form-group">
		<label for="">Additional fee Service</label>
		<input type="number" name="service_charges" class="form-control service_charges"
			   placeholder="Additional Fee" value="{{ old('service_charges',$profile->service_charges) ?? '' }}"
			   min="0" step="1" required
		/>
	</div>

@endif
