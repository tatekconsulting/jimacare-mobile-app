@extends('app.template.layout')

@section('content')
<style>
	.job-status-tabs {
		background: #fff;
		padding: 15px;
		border-radius: 8px;
		box-shadow: 0 2px 4px rgba(0,0,0,0.1);
	}
	
	.job-status-tabs .nav-tabs {
		border-bottom: 2px solid #e0e0e0;
	}
	
	.job-status-tabs .nav-link {
		color: #666;
		border: none;
		border-bottom: 3px solid transparent;
		padding: 12px 20px;
		font-weight: 600;
		transition: all 0.3s ease;
	}
	
	.job-status-tabs .nav-link:hover {
		color: #007bff;
		border-bottom-color: #007bff;
		background: #f8f9fa;
	}
	
	.job-status-tabs .nav-link.active {
		color: #007bff;
		border-bottom-color: #007bff;
		background: transparent;
	}
	
	.job-status-tabs .badge {
		font-size: 12px;
		padding: 4px 8px;
	}
</style>
	<div class="blog-head mb-3">
		<div class="container py-3 bg-white">
			<form method="get" class="row mx-n1 location-autofill">
				<div class="form-group col-12 col-md-3 px-1">
					<label for="type">I'm looking for</label>
					<select name="type"
							id="type" class="type type_filter custom-select"
					>
						<option value="">Please Select</option>
						@foreach($roles as $role)
							<option value="{{ $role->id }}"
									@if($role->id == request('type')) selected @endif
							>{{ ucfirst($role->title) }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-12 col-md px-1">
					<label for="radius">Within</label>
					@php
						$radiuses = [
							1 => '1 Mile',
							2 => '2 Miles',
							3 => '3 Miles',
							4 => '4 Miles',
							5 => '5 Miles',
							7 => '7 Miles',
							10 => '10 Miles',
						];
					@endphp
					<select name="radius"
							id="radius" class="radius custom-select"
					>
						<option value="">First Enter location than select radius</option>
						@foreach($radiuses as $key => $title)
							<option value="{{ $key }}"
									@if($key == request('radius')) selected @endif
							>{{ ucfirst($title) }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-12 col-md px-1">
					<label for="address">Location</label>
					<input type="text" name="address" value="{{ request('address') }}"
						   id="address" class="address form-control"
						   placeholder="Location"
					/>
				</div>
				<input type="hidden" name="lat" class="lat" value="{{ request('lat') }}" />
				<input type="hidden" name="long" class="long" value="{{ request('long') }}" />

				<div class="form-group col-12 col-md-4 px-1">
					<label for="type">Filter</label>
					<select name="experience"
							id="experience" class="experience experience_filter custom-select"
					>
						<option value="">All Results</option>
						@foreach($experiences as $experience)
							<option value="{{ $experience->id }}" data-type="{{ $experience->role_id }}"
									@if( $experience->id == request('experience') ) selected @endif
									@if( request('type') != $experience->role_id) class="d-none" @endif
							>{{ ucfirst($experience->title) }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-12 col-md px-1">
					<label for="postcode" class="d-none d-md-block">&nbsp;&nbsp;&nbsp;</label>
					<button class="btn btn-outline-primary btn-block" type="submit">Search</button>
				</div>
			</form>
		</div>
	</div>

	<div class="seller-listing">
		<div class="container">
			{{-- Job Status Tabs (for Clients and Admins only) --}}
			@if(isset($isClient) && $isClient || isset($isAdmin) && $isAdmin)
				<div class="job-status-tabs mb-4">
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item">
							<a class="nav-link {{ ($filter ?? 'available') === 'available' ? 'active' : '' }}" 
							   href="{{ route('contract.index', array_merge(request()->except('filter', 'page'), ['filter' => 'available'])) }}">
								<i class="fa fa-clock"></i> Available Jobs
								@if(isset($availableCount))
									<span class="badge badge-primary ml-2">{{ $availableCount }}</span>
								@endif
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link {{ ($filter ?? 'available') === 'accepted' ? 'active' : '' }}" 
							   href="{{ route('contract.index', array_merge(request()->except('filter', 'page'), ['filter' => 'accepted'])) }}">
								<i class="fa fa-check-circle"></i> Filled Jobs
								@if(isset($acceptedCount))
									<span class="badge badge-success ml-2">{{ $acceptedCount }}</span>
								@endif
							</a>
						</li>
					</ul>
				</div>
			@endif

			{{-- Informational Messages --}}
			@if(isset($isClient) && $isClient)
				<div class="alert alert-info mb-4">
					<i class="fa fa-info-circle"></i> <strong>Note:</strong> You are viewing only your own posted jobs. Other clients cannot see your jobs, and you cannot see theirs.
				</div>
			@elseif(isset($isAdmin) && $isAdmin)
				<div class="alert alert-warning mb-4">
					<i class="fa fa-eye"></i> <strong>Admin View:</strong> You can see all jobs. Use the tabs above to filter between available and accepted jobs.
				</div>
			@elseif(isset($isCarer) && $isCarer)
				<div class="alert alert-success mb-4">
					<i class="fa fa-check-circle"></i> <strong>Available Jobs:</strong> Only jobs that haven't been accepted yet are shown here.
				</div>
			@endif

			{{-- Current Filter Display --}}
			@if(isset($filter) && $filter === 'accepted')
				<div class="alert alert-success mb-3">
					<i class="fa fa-info-circle"></i> <strong>Showing Filled Jobs:</strong> These jobs have been filled and a candidate has been selected.
				</div>
			@else
				<div class="alert alert-primary mb-3">
					<i class="fa fa-info-circle"></i> <strong>Showing Available Jobs:</strong> These jobs are still open and accepting applications.
				</div>
			@endif

			@if($contracts->isEmpty())
				<div class="alert alert-info text-center py-5">
					<h4>No jobs found</h4>
					@if(isset($isClient) && $isClient)
						<p>You haven't posted any jobs yet. <a href="{{ route('contract.create') }}">Post your first job</a> to get started!</p>
					@else
						<p>No available jobs match your search criteria. Try adjusting your filters.</p>
					@endif
				</div>
			@endif

			<div class="row seller-listing">
				@foreach($contracts as $contract)
					@php
						// Check if job is filled
						$isFilled = $contract->filled_at !== null;
						$applicationCount = \App\Models\JobApplication::where('contract_id', $contract->id)
							->where('status', 'pending')
							->count();
						$selectedApplication = $contract->selectedApplication;
					@endphp
					<div class="col-12 py-3 mb-3 bg-white">
						<div class="row">
							@if( (auth()->user()->role->slug ?? '') == 'admin')
								<div class="seller-left col-12 col-md-3">
									<div class="seller-img">
										<img class="img img-fluid seller-avatar" src="{{ asset($contract->user->profile ?? 'img/undraw_profile.svg') }}"
											 alt="{{ $contract->user->firstname ?? '' }}">
									</div>
								</div>
							@endif
							<div
								class="seller-center col-12 @if( (auth()->user()->role->slug ?? '') == 'admin') col-md-6 @else col-md-9 @endif text-center text-md-left">
								<div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
									<h1 class="mb-0">{{ @$contract->user->name ?explode(' ',trim($contract->user->name))[0]: '' }}</h1>
									@if($isFilled)
										<span class="badge badge-success">
											<i class="fa fa-check-circle"></i> Filled
										</span>
										@if($selectedApplication && $selectedApplication->carer)
											<span class="badge badge-info">
												<i class="fa fa-user"></i> Selected: {{ $selectedApplication->carer->firstname }}
											</span>
										@endif
									@else
										<span class="badge badge-primary">
											<i class="fa fa-clock"></i> Available
										</span>
										@if(isset($isClient) && $isClient && $applicationCount > 0)
											<span class="badge badge-warning">
												<i class="fa fa-users"></i> {{ $applicationCount }} {{ $applicationCount == 1 ? 'Application' : 'Applications' }}
											</span>
										@endif
									@endif
								</div>
								<p>
									<span class="fa fa-map-marker mr-2"></span>
									{{ $contract->user->city ?? '' }}, {{ $contract->user->country ?? '' }}
									, {{ isset($contract->user->postcode)?substr($contract->user->postcode,0,3): '' }}

									@if(auth()->user() && auth()->user()->role_id==1 && strlen($contract->company) > 0)
										<span class="profile-seperator">|</span>
										<span class="fa fa-building mr-2"></span> {{ $contract->company }}
									@endif
									@if(request('lat') && request('long'))
										<span class="profile-seperator">|</span>
										{{ $contract->miles ?? 0 }} Miles Away
									@endif
								</p>
								<div class="profile-badge-info">
									<span class="profile-badge bg-primary text-white"><span class="fa fa-check-circle"></span>Verified & Approved</span>
									{{--<span class="profile-badge bg-primary text-white"><span class="fa fa-check-circle"></span>Insured</span>--}}
								</div>
								<p class="mt-3">
									{{ $contract->desc ?? '' }}
								</p>
								
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
									$rawProviderRate = ($clientRate * 66.6667) / 100;
									$isMinimumEnforced = $rateInfo['type'] === 'hourly' && $rawProviderRate < $minimumRate;
								@endphp
								
								@if($rateInfo['rate'] > 0)
									<div class="mt-3">
										<div class="rate-display-box p-3 bg-light rounded">
											<p class="mb-1 text-muted small">Payment Rate</p>
											<p class="h4 text-primary mb-0">
												<strong>{{ $rateInfo['formatted'] }}</strong>
											</p>
											@if($showProviderRate)
												<small class="text-muted">
													<i class="fa fa-info-circle"></i> 
													Your rate (66.6% of client's price)
													@if($isMinimumEnforced)
														<br><strong class="text-success">✓ Min £{{ number_format($minimumRate, 2) }}/hr guaranteed</strong>
													@endif
													<br>
													<small>Client posted: £{{ number_format($clientRate, 2) }}/{{ $rateInfo['type'] }}</small>
												</small>
											@elseif($isAdmin)
												@php
													$pricingBreakdown = $contract->getPricingBreakdown();
												@endphp
												<small class="text-muted">
													Provider: £{{ number_format($pricingBreakdown['provider_rate'], 2) }} | 
													Platform Fee: £{{ number_format($pricingBreakdown['platform_fee'], 2) }}
												</small>
											@endif
										</div>
									</div>
								@endif
							</div>
							<div class="seller-right col-12 col-md-3 text-center text-md-right flex-right">
								<div>
									<a href="{{ route('contract.show', ['contract' => $contract->id]) }}" class="btn btn-danger mb-2">
										<span class="fa fa-info mr-2"></span> Show Job
									</a>
									@if(isset($isClient) && $isClient && !$isFilled && $applicationCount > 0)
										<a href="{{ route('job-applications.index') }}" class="btn btn-success btn-block">
											<i class="fa fa-users mr-2"></i> View Applications ({{ $applicationCount }})
										</a>
									@endif
									@if(isset($isClient) && $isClient && !$isFilled && $applicationCount == 0)
										<form action="{{ route('contract.repost', ['contract' => $contract->id]) }}" method="POST" class="mt-2">
											@csrf
											<button type="submit" class="btn btn-outline-primary btn-block" 
													onclick="return confirm('Are you sure you want to repost this job?')">
												<i class="fa fa-redo mr-2"></i> Repost Job
											</button>
										</form>
									@endif
								</div>
							</div>
						</div>
					</div>
				@endforeach
			</div>
			<div class="row py-5">
				<div class="col-12">
					{{ $contracts->links('app.template.pagination') }}
				</div>
			</div>
		</div>
	</div>
@endsection
