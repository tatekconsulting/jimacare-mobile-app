@extends('app.template.layout')

@section('content')
	<div class="full-width mt-4 profile-management">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="about-you">
						<div class="step-wrap">
							<form method="POST" action="{{ route('profile.personal') }}" enctype="multipart/form-data"
								  id="about1" class="row about-form novalidate">
								@csrf
								<div class="form-group col-12 col-md-3">
									<label class="upload-label mx-auto">
										<div class="upload-img">
											<img src="{{ $profile->profile ?? asset('img/plus-icon.svg') }}" alt="">
										</div>
										<input type="file" name="profile"
											   id="profile" class="d-none"
											   accept="image/*"
										/>
									</label>
								</div>
								<div class="col-12 col-md-9">
									<div class="row">
										<div class="form-group col-12 col-md-6">
											<label for="firstname">First Name</label>
											<input type="text" name="firstname" value="{{ $profile->firstname ?? '' }}"
												   id="firstname" class="firstname form-control"
												   placeholder="First Name" min="4" required
											/>
										</div>
										<div class="form-group col-12 col-md-6">
											<label for="lastname">Last Name</label>
											<input type="text" name="lastname" value="{{ $profile->lastname ?? '' }}"
												   id="lastname" class="lastname form-control"
												   placeholder="Last Name" min="4" required
											/>
										</div>
										<div class="form-group col-12 col-md-6">
											<label for="email">Email</label>
											<input type="email" name="email" value="{{ $profile->email ?? '' }}"
												   id="email" class="email form-control"
												   placeholder="Email Address" min="4" required
											/>
										</div>
										<div class="form-group col-12 col-md-6">
											<label for="phone">Phone Number </label>
											<input type="tel" name="phone" value="{{ $profile->phone ?? '' }}"
												   id="phone" class="phone form-control"
												   placeholder="Phone Number" min="4" required
											/>
										</div>

										<div class="form-group col-12">
											<label for="address">Address</label>
											<input type="text" name="address" value="{{ $profile->address ?? '' }}"
												   id="address" class="address form-control"
												   placeholder="Address" row="4" required
											/>
										</div>

										<div class="form-group col-12 col-md-6">
											<label for="city">City</label>
											<input type="text" name="city" value="{{ $profile->city ?? '' }}"
												   id="city" class="city form-control"
												   placeholder="City" required
											/>
										</div>

										<div class="form-group col-12 col-md-6">
											<label for="state">State</label>
											<input type="text" name="state" value="{{ $profile->state ?? '' }}"
												   id="state" class="state form-control"
												   placeholder="State" required
											/>
										</div>

										<div class="form-group col-12 col-md-6">
											<label for="country">Country </label>
											<select name="country"
													id="country" class="country custom-select"
													required
											>
												<option value="">Country</option>
												@foreach(\App\Models\Country::all() as $country)
													<option value="{{ $country->id }}"
															@if($country->id == $profile->country_id) selected @endif
													>{{ ucfirst($country->title) }}</option>
												@endforeach
											</select>
										</div>

										<div class="form-group col-12 col-md-6">
											<label for="postcode">Postcode</label>
											<input type="text" name="postcode" value="{{ $profile->postcode ?? '' }}"
												   id="postcode" class="postcode form-control"
												   placeholder="Postcode" required
											/>
										</div>


										<div class="form-group col-12">
											Gender &nbsp; &nbsp; &nbsp;
											<div class="custom-control custom-radio custom-control-inline">
												<input type="radio" name="gender" value="male"
													   id="male" class="gender custom-control-input"
													   @if($profile->gender == 'male') checked @endif required
												/>
												<label class="custom-control-label" for="male">Male</label>
											</div>
											<div class="custom-control custom-radio custom-control-inline">
												<input type="radio" name="gender" value="female"
													   id="female" class="gender custom-control-input"
													   @if($profile->gender == 'female') checked @endif required
												/>
												<label class="custom-control-label" for="female">Female</label>
											</div>
										</div>
										<div class="form-group col-12 col-md-6">
											<label for="dob">Date of Birth </label>
											<input type="date" name="dob" value="{{ $profile->dob ?? '' }}"
												   id="dob" class="dob form-control"
												   placeholder="Date of Birth" required
											/>
										</div>
										<div class="form-group col-12 col-md-6">
											<label for="insurance">National Insurance Number</label>
											<input type="text" name="insurance" value="{{ $profile->insurance ?? '' }}"
												   id="insurance" class="insurance form-control"
												   placeholder="National Insurance Number" required
											>
										</div>

										<div class="form-group col-12">
											<label>Language</label>
											<div>
												@foreach(\App\Models\Language::all() as $lang)
													<label class="btn btn-outline-primary" data-toggle="button"
														   @if(in_array($lang->id, $profile->languages->pluck('id')->toArray() )) aria-pressed="true"
														   @else aria-pressed="false" @endif
													>
														<input type="checkbox" name="language[]" value="{{ $lang->id }}"
															   id="language_{{ $lang->id }}"
															   class="language language-checkbox"
															   @if(in_array($lang->id, $profile->languages->pluck('id')->toArray() )) checked
															   @endif
															   @if( count($profile->languages->pluck('id')) < 1 ) required @endif
														/> {{ $lang->title }}
													</label>
												@endforeach
											</div>
										</div>
									</div>
								</div>
								@if($profile->role == 'care')
									<div class="form-group col-12">
										<button class="btn btn-primary disabled" type="button"><span
												class="fa fa-long-arrow-left mr-2"></span> Prev
										</button>
										<button class="btn btn-primary float-right next-step" type="submit">Next <span
												class="fa fa-long-arrow-right ml-2"></span></button>
									</div>
								@else
									<div class="form-group col-12">
										<button class="btn btn-primary float-right" type="submit">Submit <span
												class="fa fa-long-arrow-right ml-2"></span></button>
									</div>
								@endif
							</form>
							@if($profile->role == 'care')
								<form method="POST" action="{{ route('profile.interests') }}"
									  enctype="multipart/form-data" id="about2" class="row about-form d-none">
									@csrf
									{{--<div class="form-group col-12">
										<label>Services Type</label>
										<div>
											@foreach(\App\Models\Type::all() as $type)
												<label for="type_{{ $type->id }}" class="btn btn-outline-primary" data-toggle="button" aria-pressed="false">
													<input type="checkbox" class="d-none" name="type[]" id="type_{{ $type->id }}"> {{ $type->name }}
												</label>
											@endforeach
										</div>
									</div>--}}

									<div class="form-group col-12">
										<label>Choose the experience you have</label>
										<div>
											@foreach(\App\Models\Experience::all() as $exp)
												<label for="experience_{{ $exp->id }}" class="btn btn-outline-primary"
													   data-toggle="button"
													   @if(in_array($exp->id, $profile->experiences->pluck('id')->toArray() )) aria-pressed="true"
													   @else aria-pressed="false" @endif
												>
													<input type="checkbox" name="experience[]" value="{{ $exp->id }}"
														   id="experience_{{ $exp->id }}" class="experience"
														   @if(in_array($exp->id, $profile->experiences->pluck('id')->toArray() )) checked
														   @endif
														   @if( count($profile->experiences->pluck('id')) < 1 ) required @endif
													/>{{ $exp->name }}
												</label>
											@endforeach
										</div>
									</div>

									<div class="form-group col-12">
										<label>Choose the skills you have</label>
										<div>
											@foreach(\App\Models\Skill::all() as $skill)
												<label for="skill_{{ $skill->id }}" class="btn btn-outline-primary"
													   data-toggle="button"
													   @if(in_array($skill->id, $profile->skills->pluck('id')->toArray() )) aria-pressed="true"
													   @else aria-pressed="false" @endif
												>
													<input type="checkbox" name="skill[]" value="{{ $skill->id }}"
														   id="skill_{{ $skill->id }}" class="skill"
														   @if(in_array($skill->id, $profile->skills->pluck('id')->toArray() )) checked
														   @endif
														   @if( count($profile->skills->pluck('id')) < 1 ) required @endif
													/>{{ $skill->name }}
												</label>
											@endforeach
										</div>
									</div>

									<div class="form-group col-12">
										<label>Choose your interests</label>
										<div>
											@foreach(\App\Models\Interest::all() as $interest)
												<label for="interest_{{ $interest->id }}"
													   class="btn btn-outline-primary" data-toggle="button"
													   @if(in_array($interest->id, $profile->interests->pluck('id')->toArray() )) aria-pressed="true"
													   @else aria-pressed="false" @endif
												>
													<input type="checkbox" name="interest[]" value="{{ $interest->id }}"
														   id="interest_{{ $interest->id }}" class="interest"
														   @if(in_array($interest->id, $profile->interests->pluck('id')->toArray() )) checked
														   @endif
														   @if( count($profile->interests->pluck('id')) < 1 ) required @endif
													/> {{ $interest->name }}
												</label>
											@endforeach
										</div>
									</div>

									<div class="form-group col-12">
										<button class="btn btn-primary prev-step" type="button"><span
												class="fa fa-long-arrow-left mr-2"></span> Prev
										</button>
										<button class="btn btn-primary float-right next-step" type="submit">Next <span
												class="fa fa-long-arrow-right ml-2"></span></button>
									</div>

								</form>
								<form method="POST" action="{{ route('profile.availability') }}"
									  enctype="multipart/form-data" id="about3" class="row about-form d-none">
									@csrf
									<div class="col-12">
										<div class="row">
											<div class="col-12">Availability</div>
										</div>
										@foreach(\App\Models\Type::all() as $type)
											@php
												$avail = $profile->availabilities
													->where('type_id', $type->id)
													->first()
												;
											@endphp
											<div class="row">
												<div class="col-4 col-md-4 form-group py-2">
													<div class="custom-control custom-checkbox">
														<input type="checkbox"
															   name="availability[{{$type->id}}][available]" value="1"
															   id="availability_{{$type->id}}_available"
															   class="custom-control-input available"
															   @if($avail && $avail->available ) checked @endif
														/>
														<label class="custom-control-label"
															   for="availability_{{$type->id}}_available">{{ ucfirst($type->name) }}</label>
													</div>
												</div>
												<div class="col-8 col-md-4 form-group ml-auto">
													<input type="number" name="availability[{{ $type->id }}][charges]"
														   class="form-control charges"
														   placeholder="{{ $type->name }} Charges"
														   @if($avail && $avail->charges ) value="{{ $avail->charges }}" @endif
													/>
												</div>
											</div>
										@endforeach
									</div>

									<div class="form-group col-12">
										<label for="explain_cleaning">What’s your cleaning experience
											(Description)?</label>
										<textarea name="explain_cleaning" rows="4"
												  id="explain_cleaning" class="form-control explain_cleaning"
												  placeholder="Describe your cleaning experience" required
										>{{ $profile->explain_cleaning }}</textarea>
									</div>

									<div class="form-group col-12">
										<label for="explain_housekeep">Why you would like to join Housekeep?</label>
										<textarea name="explain_housekeep" rows="4"
												  id="explain_housekeep" class="form-control explain_housekeep"
												  placeholder="Why you would like to join Housekeep?" required
										>{{ $profile->explain_housekeep }}</textarea>
									</div>

									<div class="form-group col-12">
										<label for="explain_carer">Tell us what makes you a Great Carer</label>
										<textarea name="explain_carer" rows="4"
												  id="explain_carer" class="form-control explain_carer"
												  placeholder="Tell us what makes you a Great Carer"
										>{{ $profile->explain_carer }}</textarea>
									</div>

									<div class="form-group col-12">
										<button class="btn btn-primary prev-step" type="button"><span
												class="fa fa-long-arrow-left mr-2"></span> Prev
										</button>
										<button class="btn btn-primary float-right final-step" type="submit">Submit
											<span class="fa fa-long-arrow-right ml-2"></span></button>
									</div>
								</form>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
