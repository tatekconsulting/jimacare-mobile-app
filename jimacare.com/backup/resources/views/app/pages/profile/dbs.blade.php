@if($profile->role_id > 2 )

	<div class="form-group col-12">
		Do you have a DBS Certificate?<br />
		<div class="custom-control custom-radio custom-control-inline">
			<input type="radio" name="dbs" value="yes"
				   id="dbs_yes" class="dbs custom-control-input"
				   @if($profile->dbs == true) checked @endif required
			/>
			<label class="custom-control-label" for="dbs_yes">Yes</label>
		</div>
		<div class="custom-control custom-radio custom-control-inline">
			<input type="radio" name="dbs" value="no"
				   id="dbs_no" class="dbs custom-control-input"
				   @if($profile->dbs == false) checked @endif required
			/>
			<label class="custom-control-label" for="dbs_no">No</label>
		</div>
	</div>

	<div class="form-group col-12 col-md-6">
		<label for="dbs_type">What type of DBS ?</label>
		<select name="dbs_type"
				id="dbs_type" class="dbs_type custom-select"
				@if($profile->dbs == true) required @endif
		>
			@foreach(['basic', 'standard', 'enhanced'] as $t)
				<option @if($profile->dbs_type == $t) selected @endif value="{{ $t }}">{{ ucfirst($t) }}</option>
			@endforeach
		</select>
	</div>

	<div class="form-group col-12 col-md-6">
		<label for="dbs_issue">Date of Issue </label>
		<input type="date" name="dbs_issue" value="{{ $profile->dbs_issue ?? '' }}"
			   id="date_of_issue" class="dbs_issue form-control"
			   placeholder="Date of Issue"
			   @if($profile->dbs == true) required @endif
		/>
	</div>

	<div class="form-group col-12">
		<label for="dbs_cert">Certificate Number</label>
		<input type="text" name="dbs_cert" value="{{ $profile->dbs_cert ?? '' }}"
			   id="dbs_cert" class="dbs_cert form-control"
			   placeholder="Certificate Number" min="4"
			   @if($profile->dbs == true) required @endif
		/>
	</div>
@endif
