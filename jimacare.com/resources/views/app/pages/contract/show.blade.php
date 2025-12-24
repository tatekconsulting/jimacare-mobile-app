@extends('app.template.layout')

@section('content')
	<div class="pt-5">
		<div class="container">
			@if(session('success'))
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					{{ session('success') }}
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			@endif
			@if(session('error'))
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					{{ session('error') }}
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			@endif
			<div class="row">
				<div class="col-12 py-3 bg-white">
					<div class="row">
						@if($contract->user)
							@if( (auth()->user()->role->slug ?? '') == 'admin' && $contract->user->profile)
								<div class="seller-left col-12 col-md-3">
									<div class="seller-img">
										<img class="img img-fluid seller-avatar" src="{{ asset($contract->user->profile) }}"
											 alt="{{ $contract->user->name ?? '' }}">
									</div>
								</div>
							@endif
							<div class="seller-center col-12 col-md-6 text-center text-md-left">
								<h1>{{ $contract->user->name ? explode(' ',trim($contract->user->name))[0] : '' }}</h1>
								<p>
									<span class="fa fa-map-marker mr-2"></span>{{ $contract->user->city ?? '' }}, {{ $contract->user->country ?? '' }}
									, {{ $contract->user->posttal ?? '' }}
									@if(auth()->user() && auth()->user()->role_id==1 && strlen($contract->company ?? '') > 0)
										<span class="profile-seperator">|</span>
										<span class="fa fa-building mr-2"></span> {{ $contract->company }}
									@endif
								</p>
								<div class="profile-badge-info">
									@if($contract->user->approved ?? false)
										<span class="profile-badge bg-primary text-white"><span class="fa fa-check-circle"></span>Verified & Approved</span>
									@endif

								</div>
								<p>
									{{ $contract->desc ?? '' }}
								</p>
							</div>
						@else
							<div class="seller-center col-12 col-md-9 text-center text-md-left">
								<h1>{{ $contract->title }}</h1>
								<p>{{ $contract->desc ?? '' }}</p>
							</div>
						@endif
						<div class="seller-right col-12 col-md-3 text-center text-md-right">
							@if(auth()->check())
								@php
									$userRole = auth()->user()->role->slug ?? '';
									$isCarer = in_array($userRole, ['carer', 'childminder', 'housekeeper']);
									$isClient = $userRole === 'client';
									$hasApplied = $isCarer ? \App\Models\JobApplication::where('contract_id', $contract->id)->where('carer_id', auth()->id())->exists() : false;
									$application = $isCarer ? \App\Models\JobApplication::where('contract_id', $contract->id)->where('carer_id', auth()->id())->first() : null;
									$hasInvitation = $isCarer ? \App\Models\JobInvitation::where('contract_id', $contract->id)->where('carer_id', auth()->id())->where('status', 'pending')->exists() : false;
									$invitation = $isCarer ? \App\Models\JobInvitation::where('contract_id', $contract->id)->where('carer_id', auth()->id())->where('status', 'pending')->first() : null;
								@endphp
								
								@if($isCarer)
									@if($hasInvitation && $invitation)
										<!-- Invitation Received -->
										<div class="alert alert-warning text-center py-2 mb-2">
											<small>
												<strong>📨 You've been invited!</strong><br>
												{{ $invitation->client->firstname ?? 'Client' }} invited you to this job
											</small>
										</div>
										<form action="{{ route('job-invitation.accept', $invitation->id) }}" method="POST" class="d-inline-block w-100 mb-2">
											@csrf
											<button type="submit" class="btn btn-success badge-pill btn-block">
												✓ Accept Invitation
											</button>
										</form>
										<form action="{{ route('job-invitation.reject', $invitation->id) }}" method="POST" class="d-inline-block w-100">
											@csrf
											<button type="submit" class="btn btn-danger badge-pill btn-block" onclick="return confirm('Are you sure you want to reject this invitation?')">
												✗ Reject Invitation
											</button>
										</form>
									@elseif(!$hasApplied)
										<!-- Accept/Reject Job Buttons for Carers -->
										<form action="{{ route('job.accept', $contract->id) }}" method="POST" class="d-inline-block w-100 mb-2">
											@csrf
											<button type="submit" class="btn btn-success badge-pill btn-block" onclick="return confirm('Accept this job? This will create an application automatically.')">
												✓ Accept Job
											</button>
										</form>
										<form action="{{ route('job.reject', $contract->id) }}" method="POST" class="d-inline-block w-100 mb-2">
											@csrf
											<button type="submit" class="btn btn-danger badge-pill btn-block" onclick="return confirm('Reject this job? You can still apply later if you change your mind.')">
												✗ Reject Job
											</button>
										</form>
										<!-- Or Apply with Cover Letter -->
										<button class="btn btn-primary badge-pill btn-block" data-toggle="modal" data-target="#applyModal">
											📋 Apply with Cover Letter
										</button>
									@else
										<!-- Already Applied/Accepted/Rejected -->
										<div class="alert alert-info text-center py-2 mb-2">
											<small>
												@if($application->status === 'accepted')
													✓ <strong>Job Accepted</strong><br>
													You can now message the client
												@elseif($application->status === 'rejected')
													✗ <strong>Job Rejected</strong><br>
													You can still apply again
												@else
													📋 <strong>Application Pending</strong><br>
													Waiting for client response
												@endif
											</small>
										</div>
										@if($application->status === 'accepted')
											<a href="{{ route('inbox.show', ['user' => $contract->user_id]) }}" class="btn btn-primary badge-pill btn-block">
												💬 Message Client
											</a>
										@endif
									@endif
								@elseif($isClient && $contract->user_id == auth()->id())
									@php
										$isFilled = $contract->filled_at !== null;
										$applicationCount = \App\Models\JobApplication::where('contract_id', $contract->id)
											->where('status', 'pending')
											->count();
										$hasAcceptedApplication = \App\Models\JobApplication::where('contract_id', $contract->id)
											->where('status', 'accepted')
											->exists();
									@endphp
									
									@if($isFilled)
										<div class="alert alert-success mb-2">
											<small><i class="fa fa-check-circle"></i> <strong>Job Filled</strong></small>
										</div>
										@php
											$selectedApp = $contract->selectedApplication;
										@endphp
										@if($selectedApp && $selectedApp->carer)
											<p class="text-center mb-2">
												<small>Selected: <strong>{{ $selectedApp->carer->firstname }} {{ substr($selectedApp->carer->lastname ?? '', 0, 1) }}.</strong></small>
											</p>
											<a href="{{ route('inbox.show', $selectedApp->carer->id) }}" class="btn btn-primary badge-pill btn-block">
												💬 Message Selected Carer
											</a>
										@endif
									@else
										<!-- Client can invite carers or view applications -->
										<a href="{{ route('job-applications.index') }}" class="btn btn-primary badge-pill btn-block mb-2">
											📋 View Applications
											@if($applicationCount > 0)
												<span class="badge badge-warning ml-2">{{ $applicationCount }}</span>
											@endif
										</a>
										<button class="btn btn-success badge-pill btn-block mb-2" data-toggle="modal" data-target="#inviteCarerModal">
											➕ Invite Carer
										</button>
										@if($applicationCount == 0)
											<form action="{{ route('contract.repost', ['contract' => $contract->id]) }}" method="POST" class="mt-2">
												@csrf
												<button type="submit" class="btn btn-outline-primary badge-pill btn-block" 
														onclick="return confirm('Are you sure you want to repost this job? This will create a new job posting with the same details.')">
													<i class="fa fa-redo mr-2"></i> Repost Job
												</button>
											</form>
										@endif
									@endif
								@endif
								
								<!-- Direct Message Option -->
								@if($contract->user_id && $contract->user_id != auth()->id() && $contract->user && $contract->user->exists)
									<a href="{{ route('inbox.show', $contract->user_id) }}" class="btn btn-outline-primary badge-pill btn-block mt-2">
										💬 Send Message
									</a>
								@endif
							@else
								<a href="{{ route('login') }}" class="btn badge-pill btn-block btn-success float-md-right mb-2">Apply for this Job</a>
								<a href="{{ route('login') }}" class="btn badge-pill btn-block btn-primary float-md-right mb-1">Accept Job</a>
							@endif
							
							@if(auth()->check())
								<div class="text-center d-none msg-sent-notice py-3">
									<span class="fa fa-check-circle fa-2x"></span><br/>
									Message Sent
								</div>
							@endif
						</div>
					</div>
				</div>

				<div class="col-12 pt-3 pb-5">
					<div class="row">
						<div class="col-12 col-md-6">
							<h4>When to join</h4>
							<p>
								@if($contract->start_type == 'immediately')
									Immediately
								@elseif($contract->start_type == 'not-sure')
									Not Sure
								@elseif($contract->start_date)
									{{ $contract->start_date->format('d M Y') }}
								@else
									Not specified
								@endif
							</p>
						</div>

						<div class="col-12 col-md-6">
							<h4>When to end</h4>
							<p>
								@if($contract->end_type == 'on-going')
									On Going
								@elseif($contract->end_date)
									{{ $contract->end_date->format('d M Y') }}
								@else
									Not specified
								@endif
							</p>
						</div>

						<div class="col-12 col-md-6">
							<h4>Time of Arrival</h4>
							<p>{{ $contract->start_time ? $contract->start_time->format('H:i') : 'Not specified' }}</p>
						</div>

						<div class="col-12 col-md-6">
							<h4>Time of Leave</h4>
							<p>{{ $contract->end_time ? $contract->end_time->format('H:i') : 'Not specified' }}</p>
						</div>

						{{-- Payment Rate Display --}}
						@php
							$userRole = auth()->user()->role->slug ?? '';
							$isCarer = in_array($userRole, ['carer', 'childminder', 'housekeeper']);
							$isClient = $userRole === 'client' && auth()->id() == $contract->user_id;
							$isAdmin = $userRole === 'admin';
							
							// Show provider rate to service providers, client rate to clients, both to admins
							$showProviderRate = $isCarer && !$isClient && !$isAdmin;
							$rateInfo = $contract->getDisplayRate($showProviderRate);
							$clientRate = $contract->hourly_rate ?? $contract->daily_rate ?? $contract->weekly_rate ?? 0;
							$minimumRate = $contract->getMinimumProviderRate();
							$calculatedRate = $contract->calculateProviderRate($clientRate, $rateInfo['type']);
							$rawProviderRate = ($clientRate * 66.6667) / 100;
							$isMinimumEnforced = $rateInfo['type'] === 'hourly' && $rawProviderRate < $minimumRate;
						@endphp
						
						@if($rateInfo['rate'] > 0)
							@if($isAdmin)
								{{-- Admin View: Show both rates and platform fee --}}
								@php
									$pricingBreakdown = $contract->getPricingBreakdown();
								@endphp
								<div class="col-12 col-md-12">
									<h4>Pricing Breakdown</h4>
									<div class="rate-display p-3 bg-light rounded">
										<div class="row">
											<div class="col-md-4">
												<p class="mb-1 text-muted small">Client Posted Rate</p>
												<p class="h5 text-primary mb-0">
													<strong>£{{ number_format($pricingBreakdown['client_rate'], 2) }}/{{ $pricingBreakdown['type'] }}</strong>
												</p>
											</div>
											<div class="col-md-4">
												<p class="mb-1 text-muted small">Service Provider Receives (66.6%)</p>
												<p class="h5 text-success mb-0">
													<strong>£{{ number_format($pricingBreakdown['provider_rate'], 2) }}/{{ $pricingBreakdown['type'] }}</strong>
												</p>
												@if($isMinimumEnforced)
													<small class="text-success">
														✓ Minimum £{{ number_format($minimumRate, 2) }}/hr enforced
													</small>
												@endif
											</div>
											<div class="col-md-4">
												<p class="mb-1 text-muted small">Platform Fee (33.3333%)</p>
												<p class="h5 text-info mb-0">
													<strong>£{{ number_format($pricingBreakdown['platform_fee'], 2) }}/{{ $pricingBreakdown['type'] }}</strong>
												</p>
											</div>
										</div>
									</div>
								</div>
							@else
								{{-- Service Provider or Client View --}}
								<div class="col-12 col-md-6">
									<h4>Payment Rate</h4>
									<div class="rate-display">
										<p class="h5 text-primary mb-1">
											<strong>{{ $rateInfo['formatted'] }}</strong>
										</p>
										@if($showProviderRate)
											<small class="text-muted">
												<i class="fa fa-info-circle"></i> 
												This is your rate (66.6% of client's posted price).
												@if($isMinimumEnforced)
													<br><strong class="text-success">✓ Minimum rate of £{{ number_format($minimumRate, 2) }}/hour guaranteed</strong>
												@endif
												<br>
												<small>Client posted: £{{ number_format($clientRate, 2) }}/{{ $rateInfo['type'] }}</small>
											</small>
										@else
											<small class="text-muted">
												<i class="fa fa-info-circle"></i> 
												This is the rate you posted for this job.
											</small>
										@endif
									</div>
								</div>
							@endif
						@endif

						@if(($contract->role->id ?? $contract->role_id ?? 0) == 4)
							<div class="col-12 col-md-12">
								<h4>Required to Drive?</h4>
								<p>{{ $contract->drive ? 'Yes' : 'No' }}</p>
							</div>
						@endif

						@if(($contract->role->id ?? $contract->role_id ?? 0) == 3)
							<div class="col-12 pt-3">
								<h3>Services Type</h3>
								<div class="mb-1">
									@foreach($contract->types as $type)
										<span class="px-3 py-2 bg-primary text-white rounded-pill d-inline-block mr-2 mb-2">{{ $type->title }}</span>
									@endforeach
								</div>
							</div>
						@endif

						@if(($contract->role->id ?? $contract->role_id ?? 0) == 5)
							<div class="col-12 col-md-6">
								<h4>How often do you need cleaning?</h4>
								<p>{{ $contract->how_often ?? '' }}</p>
							</div>
							<div class="col-12 col-md-6">
								<h4>How many bedroom(s) need cleaning?</h4>
								<p>{{ $contract->beds ?? '' }}</p>
							</div>
							<div class="col-12 col-md-6">
								<h4>How many bathroom(s) need cleaning?</h4>
								<p>{{ $contract->baths ?? '' }}</p>
							</div>
							<div class="col-12 col-md-6">
								<h4>How many reception room(s) need cleaning?</h4>
								<p>{{ $contract->rooms ?? '' }}</p>
							</div>
							<div class="col-12 col-md-6">
								<h4>What type of cleaning would you like?</h4>
								<p>{{ $contract->cleaning_type ?? '' }}</p>
							</div>
						@endif

						@if(in_array($contract->role_id, [3,5]))
							<div class="col-12">
								<h3>Working Days</h3>
								<div class="mb-1">
									@foreach($contract->days as $day)
										<span class="px-3 py-2 bg-primary text-white rounded-pill d-inline-block mr-2 mb-2">{{ $day->title }}</span>
									@endforeach
								</div>
							</div>
						@endif

						@if($contract->role_id == 4 )
							<div class="col-12">
								<h3>Working Time</h3>
								<div class="table-responsive mb-3">
									<table class="table table-bordered table-striped  mb-0">
										<thead>
										<tr>
											<td></td>
											@foreach($days as $day)
												<td class="text-center">{{ $day->title }}</td>
											@endforeach
										</tr>
										</thead>
										<tbody>
										@foreach($time_types ?? [] as $time)
											<tr>
												<td>{{ $time->title }}</td>
												@foreach($days as $day)
													@php $avail = $contract->time_availables->where('type_id', $time->id)->where('day_id', $day->id)->count(); @endphp
													<td class="text-center">
														<span class="fa fa-lg fa-{{ ($avail > 0) ? 'check-square-o text-success' : 'times-circle text-danger' }}"></span>
													</td>
												@endforeach
											</tr>
										@endforeach
										</tbody>
									</table>
								</div>
							</div>
						@endif

						<div class="col-12">
							<h3>Languages</h3>
							<div class="mb-1">
								@foreach($contract->languages as $language)
									<span class="px-3 py-2 bg-primary text-white rounded-pill d-inline-block mr-2 mb-2">{{ $language->title }}</span>
								@endforeach
							</div>
						</div>

						<div class="col-12">
							<h3>Experiences Required</h3>
							<div class="mb-1">
								@foreach($contract->experiences as $exp)
									<span class="px-3 py-2 bg-primary text-white rounded-pill d-inline-block mr-2 mb-2">{{ $exp->title }}</span>
								@endforeach
							</div>
						</div>
						@if($contract->role_id == 4)
							<div class="col-12">
								<h3>Interests</h3>
								<div class="mb-1">
									@foreach($contract->interests as $interest)
										<span class="px-3 py-2 bg-primary text-white rounded-pill d-inline-block mr-2 mb-2">{{ $interest->title }}</span>
									@endforeach
								</div>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Invite Carer Modal -->
	@auth
		@if(auth()->user()->role->slug ?? '' === 'client' && $contract->user_id == auth()->id())
			<div class="modal fade" id="inviteCarerModal" tabindex="-1" role="dialog" aria-labelledby="inviteCarerModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<form method="POST" action="{{ route('job.invite', $contract->id) }}">
							@csrf
							<div class="modal-header">
								<h5 class="modal-title" id="inviteCarerModalLabel">Invite Carer to: {{ $contract->title }}</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label for="carer_id">Select Carer/Childminder/Housekeeper</label>
									<select name="carer_id" id="carer_id" class="form-control" required>
										<option value="">-- Select a carer --</option>
										@php
											$carers = \App\Models\User::whereIn('role_id', [3, 4, 5])
												->where('status', 'active')
												->orderBy('firstname')
												->get();
										@endphp
										@foreach($carers as $carer)
											@php
												$hasInvitation = \App\Models\JobInvitation::where('contract_id', $contract->id)
													->where('carer_id', $carer->id)
													->exists();
											@endphp
											@if(!$hasInvitation)
												<option value="{{ $carer->id }}">
													{{ $carer->firstname }} {{ $carer->lastname ?? '' }} 
													({{ $carer->role->title ?? 'N/A' }})
												</option>
											@endif
										@endforeach
									</select>
									<small class="form-text text-muted">Only carers who haven't been invited yet are shown.</small>
								</div>
								<div class="form-group">
									<label for="invite_message">Optional Message</label>
									<textarea name="message" id="invite_message" class="form-control" rows="3" 
											  placeholder="Add a personal message to your invitation..."></textarea>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
								<button type="submit" class="btn btn-primary">Send Invitation</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		@endif
	@endauth

	<!-- Apply Modal -->
	@auth
		@php
			$userRole = auth()->user()->role->slug ?? '';
			$isCarer = in_array($userRole, ['carer', 'childminder', 'housekeeper']);
		@endphp
		@if($isCarer)
			<div class="modal fade" id="applyModal" tabindex="-1" role="dialog" aria-labelledby="applyModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<form method="POST" action="{{ route('job.apply', $contract->id) }}">
							@csrf
							<div class="modal-header">
								<h5 class="modal-title" id="applyModalLabel">Apply for: {{ $contract->title }}</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label for="proposed_rate">Your Proposed Hourly Rate (£)</label>
									@php
										$userRole = auth()->user()->role->slug ?? '';
										$isAdmin = $userRole === 'admin';
										$isClient = $userRole === 'client' && auth()->id() == $contract->user_id;
										$providerRate = $contract->getProviderHourlyRate();
									@endphp
									<input type="number" step="0.01" class="form-control" id="proposed_rate" name="proposed_rate" 
										   value="{{ $providerRate > 0 ? $providerRate : ($contract->hourly_rate ?? '') }}" placeholder="e.g., 12.50">
									@if($isAdmin || $isClient)
										<small class="form-text text-muted">Job rate: £{{ number_format($contract->hourly_rate ?? 0, 2) }}/hour</small>
									@else
										@php
											$clientRate = $contract->hourly_rate ?? 0;
											$minimumRate = $contract->getMinimumProviderRate();
											$rawProviderRate = ($clientRate * 66.6667) / 100;
											$isMinimumEnforced = $rawProviderRate < $minimumRate;
										@endphp
										<small class="form-text text-muted">
											Your rate (66.6% of client's price): 
											<strong>£{{ number_format($providerRate, 2) }}/hour</strong>
											@if($isMinimumEnforced)
												<br><span class="text-success"><strong>✓ Minimum rate of £{{ number_format($minimumRate, 2) }}/hour guaranteed</strong></span>
											@endif
											<br>
											<small class="text-muted">Client posted: £{{ number_format($clientRate, 2) }}/hour</small>
										</small>
									@endif
								</div>
								<div class="form-group">
									<label for="cover_letter">Cover Letter (Optional)</label>
									<textarea class="form-control" id="cover_letter" name="cover_letter" rows="4" 
											  placeholder="Tell the client why you're a great fit for this job..."></textarea>
									<small class="form-text text-muted">Highlight your relevant experience and availability.</small>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
								<button type="submit" class="btn btn-success">Submit Application</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		@endif
	@endauth
@endsection
