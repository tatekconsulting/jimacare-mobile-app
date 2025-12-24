@extends('admin.template.layout')

@section('content')
<style>
	/* Statistics Cards */
	.stats-card {
		background: white;
		border-radius: 12px;
		padding: 1.5rem;
		box-shadow: 0 2px 10px rgba(0,0,0,0.08);
		transition: all 0.3s ease;
		border-left: 4px solid;
	}

	.stats-card:hover {
		transform: translateY(-4px);
		box-shadow: 0 4px 20px rgba(0,0,0,0.12);
	}

	.stats-card.total { border-left-color: #667eea; }
	.stats-card.active { border-left-color: #48bb78; }
	.stats-card.pending { border-left-color: #ed8936; }
	.stats-card.review { border-left-color: #4299e1; }
	.stats-card.blocked { border-left-color: #f56565; }
	.stats-card.new-month { border-left-color: #9f7aea; }

	.stats-value {
		font-size: 2rem;
		font-weight: 700;
		color: #2d3748;
		margin: 0.5rem 0;
	}

	.stats-label {
		font-size: 0.9rem;
		color: #718096;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}

	/* Advanced Filters */
	.advanced-filters {
		background: #f7fafc;
		border-radius: 12px;
		padding: 1.5rem;
		margin-bottom: 2rem;
		display: none;
	}

	.advanced-filters.show {
		display: block;
	}

	/* Bulk Actions Bar */
	.bulk-actions-bar {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: white;
		padding: 1rem 1.5rem;
		border-radius: 12px;
		margin-bottom: 1.5rem;
		display: none;
		align-items: center;
		justify-content: space-between;
	}

	.bulk-actions-bar.show {
		display: flex;
	}

	.bulk-actions-count {
		font-weight: 600;
		font-size: 1.1rem;
	}

	.bulk-actions-buttons {
		display: flex;
		gap: 0.75rem;
	}

	/* Quick Actions */
	.btn-quick-action {
		padding: 0.25rem 0.75rem;
		font-size: 0.85rem;
		border-radius: 6px;
		margin: 0 0.25rem;
	}

	/* Activity Badges */
	.activity-badge {
		display: inline-flex;
		align-items: center;
		padding: 0.25rem 0.75rem;
		border-radius: 50px;
		font-size: 0.85rem;
		font-weight: 600;
		background: #edf2f7;
		color: #4a5568;
	}

	.activity-badge.success {
		background: #c6f6d5;
		color: #22543d;
	}

	.activity-badge.warning {
		background: #feebc8;
		color: #7c2d12;
	}

	/* Status Badges */
	.status-badge {
		padding: 0.4rem 0.9rem;
		border-radius: 50px;
		font-size: 0.85rem;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}

	.status-badge.pending { background: #feebc8; color: #7c2d12; }
	.status-badge.review { background: #bee3f8; color: #1a365d; }
	.status-badge.active { background: #c6f6d5; color: #22543d; }
	.status-badge.block { background: #fed7d7; color: #742a2a; }

	/* Modern Table */
	.modern-table {
		background: white;
		border-radius: 12px;
		overflow: hidden;
		box-shadow: 0 2px 10px rgba(0,0,0,0.08);
	}

	.modern-table thead {
		background: #f7fafc;
	}

	.modern-table thead th {
		font-weight: 600;
		text-transform: uppercase;
		font-size: 0.85rem;
		letter-spacing: 0.5px;
		color: #4a5568;
		padding: 1rem;
		border-bottom: 2px solid #e2e8f0;
	}

	.modern-table tbody tr {
		transition: all 0.2s ease;
		border-bottom: 1px solid #e2e8f0;
	}

	.modern-table tbody tr:hover {
		background: #f7fafc;
	}

	.modern-table tbody td {
		padding: 1rem;
		vertical-align: middle;
	}

	/* Avatar */
	.user-avatar {
		width: 45px;
		height: 45px;
		border-radius: 50%;
		object-fit: cover;
		border: 2px solid #e2e8f0;
	}

	/* Verification Icons */
	.verification-icons {
		display: flex;
		gap: 0.5rem;
		align-items: center;
	}

	.verification-icon {
		width: 20px;
		height: 20px;
		border-radius: 50%;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		font-size: 0.7rem;
	}

	.verification-icon.verified {
		background: #48bb78;
		color: white;
	}

	.verification-icon.unverified {
		background: #cbd5e0;
		color: #718096;
	}

	/* Responsive */
	@media (max-width: 768px) {
		.stats-card {
			margin-bottom: 1rem;
		}
		
		.bulk-actions-bar {
			flex-direction: column;
			gap: 1rem;
		}
		
		.modern-table {
			font-size: 0.85rem;
		}
	}
</style>

<div class="container-fluid">
	<!-- Page Header -->
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h1 class="h3 mb-0 text-gray-800">
			<i class="fa fa-users mr-2"></i>User Management
		</h1>
	</div>

	<!-- Statistics Cards -->
	@if(isset($stats))
	<div class="row mb-4">
		<div class="col-12 col-md-6 col-lg-2 mb-3">
			<div class="stats-card total">
				<div class="stats-label">Total Users</div>
				<div class="stats-value">{{ number_format($stats['total']) }}</div>
			</div>
		</div>
		<div class="col-12 col-md-6 col-lg-2 mb-3">
			<div class="stats-card active">
				<div class="stats-label">Active</div>
				<div class="stats-value">{{ number_format($stats['active']) }}</div>
			</div>
		</div>
		<div class="col-12 col-md-6 col-lg-2 mb-3">
			<div class="stats-card pending">
				<div class="stats-label">Pending</div>
				<div class="stats-value">{{ number_format($stats['pending']) }}</div>
			</div>
		</div>
		<div class="col-12 col-md-6 col-lg-2 mb-3">
			<div class="stats-card review">
				<div class="stats-label">In Review</div>
				<div class="stats-value">{{ number_format($stats['review']) }}</div>
			</div>
		</div>
		<div class="col-12 col-md-6 col-lg-2 mb-3">
			<div class="stats-card blocked">
				<div class="stats-label">Blocked</div>
				<div class="stats-value">{{ number_format($stats['blocked']) }}</div>
			</div>
		</div>
		<div class="col-12 col-md-6 col-lg-2 mb-3">
			<div class="stats-card new-month">
				<div class="stats-label">New This Month</div>
				<div class="stats-value">{{ number_format($stats['new_this_month']) }}</div>
			</div>
		</div>
	</div>
	@endif

	<!-- Bulk Actions Bar -->
	<div class="bulk-actions-bar" id="bulkActionsBar">
		<div class="bulk-actions-count">
			<span id="selectedCount">0</span> user(s) selected
		</div>
		<div class="bulk-actions-buttons">
			<select id="bulkStatusSelect" class="form-control form-control-sm" style="width: auto; display: inline-block;">
				<option value="">Change Status To...</option>
				<option value="pending">Pending</option>
				<option value="review">Review</option>
				<option value="active">Active</option>
				<option value="block">Block</option>
			</select>
			<button class="btn btn-light btn-sm" id="bulkStatusBtn">
				<i class="fa fa-check mr-1"></i>Update Status
			</button>
			<button class="btn btn-light btn-sm" id="bulkDeleteBtn">
				<i class="fa fa-trash mr-1"></i>Delete Selected
			</button>
			<button class="btn btn-light btn-sm" id="clearSelectionBtn">
				<i class="fa fa-times mr-1"></i>Clear
			</button>
		</div>
	</div>

	<!-- Main Card -->
	<div class="card shadow mb-4">
		<div class="card-header py-3">
			<div class="row">
				<div class="col-12">
					<!-- Basic Filters -->
					<form method="get" id="filterForm" class="row">
						<div class="form-group col-12 col-md-3 mb-2 mb-md-0">
							<label for="name" class="small font-weight-bold">Search</label>
							<input type="text" name="name" id="name" class="form-control form-control-sm"
								value="{{ request('name') }}" placeholder="Name, Email, Phone" />
						</div>
						<div class="form-group col-12 col-md-2 mb-2 mb-md-0">
							<label for="type" class="small font-weight-bold">Type</label>
							<select name="type" id="type" class="form-control form-control-sm">
								<option value="">All Types</option>
								@foreach ($roles as $role)
									<option value="{{ $role->id }}" @if ($role->id == request('type')) selected @endif>
										{{ ucfirst($role->title) }}
									</option>
								@endforeach
							</select>
						</div>
						<div class="form-group col-12 col-md-2 mb-2 mb-md-0">
							<label for="status" class="small font-weight-bold">Status</label>
							<select name="status" class="form-control form-control-sm">
								<option value="">All Status</option>
								<option value="pending" @if (request('status') == 'pending') selected @endif>Pending</option>
								<option value="review" @if (request('status') == 'review') selected @endif>Review</option>
								<option value="active" @if (request('status') == 'active') selected @endif>Active</option>
								<option value="block" @if (request('status') == 'block') selected @endif>Block</option>
							</select>
						</div>
						<div class="form-group col-12 col-md-2 mb-2 mb-md-0">
							<label for="per_page" class="small font-weight-bold">Per Page</label>
							<select name="per_page" class="form-control form-control-sm">
								<option value="15" @if((request('per_page') ?? 15) == 15) selected @endif>15</option>
								<option value="25" @if(request('per_page') == 25) selected @endif>25</option>
								<option value="50" @if(request('per_page') == 50) selected @endif>50</option>
								<option value="100" @if(request('per_page') == 100) selected @endif>100</option>
								<option value="all" @if(request('per_page') == 'all') selected @endif>All</option>
							</select>
						</div>
						<div class="form-group col-12 col-md-3 mb-2 mb-md-0 d-flex align-items-end">
							<button class="btn btn-primary btn-sm mr-2" type="submit">
								<i class="fa fa-search mr-1"></i>Search
							</button>
							<button type="button" class="btn btn-outline-secondary btn-sm" id="toggleAdvancedFilters">
								<i class="fa fa-filter mr-1"></i>Advanced
							</button>
							<a href="{{ route('dashboard.user.index') }}" class="btn btn-outline-danger btn-sm ml-2">
								<i class="fa fa-times mr-1"></i>Clear
							</a>
						</div>
					</form>

					<!-- Advanced Filters -->
					<div class="advanced-filters mt-3" id="advancedFilters">
						<form method="get" class="row">
							<input type="hidden" name="name" value="{{ request('name') }}">
							<input type="hidden" name="type" value="{{ request('type') }}">
							<input type="hidden" name="status" value="{{ request('status') }}">
							<input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
							
							<div class="form-group col-12 col-md-3 mb-2">
								<label class="small font-weight-bold">City</label>
								<input type="text" name="city" class="form-control form-control-sm" value="{{ request('city') }}" />
							</div>
							<div class="form-group col-12 col-md-3 mb-2">
								<label class="small font-weight-bold">Country</label>
								<input type="text" name="country" class="form-control form-control-sm" value="{{ request('country') }}" />
							</div>
							<div class="form-group col-12 col-md-2 mb-2">
								<label class="small font-weight-bold">DBS Check</label>
								<select name="dbs" class="form-control form-control-sm">
									<option value="">All</option>
									<option value="yes" @if(request('dbs') == 'yes') selected @endif>Yes</option>
									<option value="no" @if(request('dbs') == 'no') selected @endif>No</option>
								</select>
							</div>
							<div class="form-group col-12 col-md-2 mb-2">
								<label class="small font-weight-bold">Video Verified</label>
								<select name="verified_video" class="form-control form-control-sm">
									<option value="">All</option>
									<option value="yes" @if(request('verified_video') == 'yes') selected @endif>Yes</option>
									<option value="no" @if(request('verified_video') == 'no') selected @endif>No</option>
								</select>
							</div>
							<div class="form-group col-12 col-md-2 mb-2">
								<label class="small font-weight-bold">Registered From</label>
								<input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" />
							</div>
							<div class="form-group col-12 col-md-2 mb-2">
								<label class="small font-weight-bold">Registered To</label>
								<input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" />
							</div>
							<div class="form-group col-12 col-md-2 mb-2">
								<label class="small font-weight-bold">Last Login From</label>
								<input type="date" name="last_login_from" class="form-control form-control-sm" value="{{ request('last_login_from') }}" />
							</div>
							<div class="form-group col-12 col-md-2 mb-2">
								<label class="small font-weight-bold">Last Login To</label>
								<input type="date" name="last_login_to" class="form-control form-control-sm" value="{{ request('last_login_to') }}" />
							</div>
							<div class="form-group col-12 col-md-2 mb-2 d-flex align-items-end">
								<button class="btn btn-primary btn-sm" type="submit">
									<i class="fa fa-filter mr-1"></i>Apply Filters
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="card-body">
			<!-- Export and Actions -->
			<div class="d-flex justify-content-between align-items-center mb-3">
				<div>
					<h6 class="m-0 font-weight-bold text-primary">
						Showing {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
					</h6>
					<small class="text-muted" id="selectedCount">0 selected</small>
				</div>
				<div>
					<button class="btn btn-info btn-sm mr-2" type="button" id="selectAllPagesBtn" title="Select all users across all pages">
						<i class="fa fa-check-square mr-1"></i>Select All (All Pages)
					</button>
					<button class="btn btn-secondary btn-sm mr-2" type="button" id="clearSelectionBtn">
						<i class="fa fa-times mr-1"></i>Clear Selection
					</button>
					<form method="get" action="{{ route('users.export') }}" class="d-inline" id="exportForm">
						<!-- User IDs will be added dynamically via JavaScript -->
						<button class="btn btn-success btn-sm" type="submit" id="exportBtn" disabled>
							<i class="fa fa-download mr-1"></i>Export Selected (<span id="exportCount">0</span>)
						</button>
					</form>
					<form method="get" action="{{ route('users.export') }}" class="d-inline ml-2">
						@foreach ($unpaginatedusers as $user)
							<input type="hidden" name="users[]" value="{{ $user->id }}">
						@endforeach
						<button class="btn btn-outline-success btn-sm" type="submit" title="Export all users (ignoring selection)">
							<i class="fa fa-download mr-1"></i>Export All ({{ $unpaginatedusers->count() }})
						</button>
					</form>
				</div>
			</div>

			<!-- Users Table -->
			<div class="table-responsive">
				<table class="table modern-table" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th width="40">
								<input type="checkbox" id="selectAll" title="Select all users on this page" style="cursor: pointer;" />
							</th>
							<th>ID</th>
							<th>Avatar</th>
							<th>Name</th>
							<th>Type</th>
							<th>Email</th>
							<th>Phone</th>
							<th>Location</th>
							<th>Verification</th>
							<th>Activity</th>
							<th>Status</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						@forelse ($users ?? [] as $user)
							<tr>
								<td>
									<input type="checkbox" class="user-checkbox" value="{{ $user->id }}" 
										@if($user->power_admin == true && auth()->user()->power_admin == false) disabled @endif />
								</td>
								<td>{{ $user->id }}</td>
								<td>
									@if ($user->profile ?? false)
										<img src="{{ asset($user->profile) }}" alt="" class="user-avatar">
									@else
										<div class="user-avatar bg-secondary d-flex align-items-center justify-content-center text-white">
											{{ strtoupper(substr($user->firstname ?? 'U', 0, 1)) }}
										</div>
									@endif
								</td>
								<td>
									<div class="font-weight-bold">{{ $user->firstname }} {{ $user->lastname }}</div>
									<small class="text-muted">{{ $user->email }}</small>
								</td>
								<td>
									<span class="badge badge-info">{{ $user->role->title ?? 'N/A' }}</span>
								</td>
								<td>{{ $user->email }}</td>
								<td>{{ $user->phone }}</td>
								<td>
									@if($user->city)
										{{ $user->city }}{{ $user->country ? ', ' . $user->country : '' }}
									@else
										<span class="text-muted">N/A</span>
									@endif
								</td>
								<td>
									<div class="verification-icons">
										<span class="verification-icon {{ $user->dbs ? 'verified' : 'unverified' }}" 
											title="DBS: {{ $user->dbs ? 'Verified' : 'Not Verified' }}">
											<i class="fa fa-shield"></i>
										</span>
										<span class="verification-icon {{ $user->verified_video ? 'verified' : 'unverified' }}" 
											title="Video: {{ $user->verified_video ? 'Verified' : 'Not Verified' }}">
											<i class="fa fa-video"></i>
										</span>
										<span class="verification-icon {{ $user->referee1_status && $user->referee2_status ? 'verified' : 'unverified' }}" 
											title="References: {{ $user->referee1_status && $user->referee2_status ? 'Complete' : 'Incomplete' }}">
											<i class="fa fa-user-check"></i>
										</span>
									</div>
								</td>
								<td>
									@php
										$applicationsCount = 0;
										$timesheetsCount = 0;
										
										// Safely get counts if relationships exist
										if (method_exists($user, 'jobApplications') && isset($user->jobApplications)) {
											$applicationsCount = is_countable($user->jobApplications) ? $user->jobApplications->count() : 0;
										} else {
											// Fallback: count directly from database
											try {
												$applicationsCount = \App\Models\JobApplication::where('carer_id', $user->id)->count();
											} catch (\Exception $e) {
												$applicationsCount = 0;
											}
										}
										
										if (method_exists($user, 'timesheets') && isset($user->timesheets)) {
											$timesheetsCount = is_countable($user->timesheets) ? $user->timesheets->count() : 0;
										} else {
											// Fallback: count directly from database
											try {
												$timesheetsCount = \App\Models\Timesheet::where('carer_id', $user->id)->count();
											} catch (\Exception $e) {
												$timesheetsCount = 0;
											}
										}
									@endphp
									<div>
										<span class="activity-badge {{ $applicationsCount > 0 ? 'success' : '' }}">
											<i class="fa fa-briefcase mr-1"></i>{{ $applicationsCount }} Apps
										</span>
										<span class="activity-badge {{ $timesheetsCount > 0 ? 'success' : '' }}">
											<i class="fa fa-clock mr-1"></i>{{ $timesheetsCount }} Timesheets
										</span>
									</div>
									@if($user->last_login)
										<small class="text-muted d-block mt-1">
											Last: {{ $user->last_login->diffForHumans() }}
										</small>
									@endif
								</td>
								<td>
									<form action="{{ route('dashboard.user.status', ['user' => $user->id]) }}" method="post" class="status-form">
										@csrf
										<select name="status" class="form-control form-control-sm status-autoupate" 
											@if ($user->power_admin == true && auth()->user()->power_admin == false) disabled @endif>
											@foreach (['pending', 'review', 'active', 'block'] as $s)
												<option value="{{ $s }}" @if ($s == $user->status) selected @endif>
													{{ ucfirst($s) }}
												</option>
											@endforeach
										</select>
									</form>
								</td>
								<td>
									<div class="btn-group btn-group-sm">
										<a href="{{ route('dashboard.user.edit', ['user' => $user->id]) }}" 
											class="btn btn-primary btn-quick-action" title="Edit">
											<i class="fa fa-edit"></i>
										</a>
										@if(in_array($user->role_id, [3, 4, 5]))
											<a href="{{ route('seller.show', ['user' => $user->id]) }}" 
												target="_blank"
												class="btn btn-info btn-quick-action" title="View Profile">
												<i class="fa fa-eye"></i>
											</a>
											@if($timesheetsCount > 0)
												<a href="{{ url('/carer/timesheets?carer_id=' . $user->id) }}" 
													target="_blank"
													class="btn btn-warning btn-quick-action" title="View Timesheets">
													<i class="fa fa-clock"></i>
												</a>
											@endif
											@if($applicationsCount > 0)
												<a href="{{ url('/job-applications?carer_id=' . $user->id) }}" 
													target="_blank"
													class="btn btn-secondary btn-quick-action" title="View Applications">
													<i class="fa fa-briefcase"></i>
												</a>
											@endif
										@endif
										@if ($user->power_admin == false)
											<a href="{{ route('dashboard.user.destroy', ['user' => $user->id]) }}"
												class="btn btn-danger btn-quick-action btn-delete"
												onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this user?') == true) document.getElementById('user-{{ $user->id }}').submit();"
												title="Delete">
												<i class="fa fa-trash"></i>
											</a>
											<form id="user-{{ $user->id }}"
												action="{{ route('dashboard.user.destroy', ['user' => $user->id]) }}"
												method="POST" class="d-none">
												@csrf
												@method('delete')
											</form>
										@endif
									</div>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="12" class="text-center py-5">
									<i class="fa fa-users fa-3x text-muted mb-3"></i>
									<p class="text-muted">No users found matching your criteria.</p>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			<!-- Pagination -->
			@if($users->hasPages())
			<div class="mt-3">
				{{ $users->appends($_GET)->links('pagination.default') }}
			</div>
			@endif
		</div>
	</div>
</div>

@push('scripts')
<script>
	(function() {
		// Wait for DOM to be ready
		function initUserSelection() {
			// Advanced Filters Toggle
			const toggleFilters = document.getElementById('toggleAdvancedFilters');
			if (toggleFilters) {
				toggleFilters.addEventListener('click', function() {
					const filters = document.getElementById('advancedFilters');
					if (filters) {
						filters.classList.toggle('show');
					}
				});
			}

			// Select All Checkbox
			const selectAll = document.getElementById('selectAll');
			const userCheckboxes = document.querySelectorAll('.user-checkbox');
			
			if (!selectAll) {
				console.error('Select All checkbox not found!');
				return;
			}
			
			if (userCheckboxes.length === 0) {
				console.warn('No user checkboxes found!');
				return;
			}
			
			console.log('Initializing user selection:', userCheckboxes.length, 'checkboxes found');
			
			// Select All Checkbox Event
			selectAll.addEventListener('change', function(e) {
				const isChecked = e.target.checked;
				console.log('Select All checkbox clicked:', isChecked);
				
				// Select/deselect all visible checkboxes
				userCheckboxes.forEach(cb => {
					if (!cb.disabled) {
						cb.checked = isChecked;
					}
				});
				
				// If unchecking, also clear sessionStorage
				if (!isChecked) {
					sessionStorage.removeItem('selectedUserIds');
				}
				
				updateBulkActionsBar();
			});

			// Individual Checkbox Change
			userCheckboxes.forEach(cb => {
				cb.addEventListener('change', function() {
					updateBulkActionsBar();
					// Update select all state
					const allChecked = Array.from(userCheckboxes).every(c => c.checked || c.disabled);
					const someChecked = Array.from(userCheckboxes).some(c => c.checked && !c.disabled);
					if (selectAll) {
						selectAll.checked = allChecked && someChecked;
						selectAll.indeterminate = someChecked && !allChecked;
					}
				});
			});

			// Update Bulk Actions Bar and Export Button
			function updateBulkActionsBar() {
				const selected = Array.from(userCheckboxes).filter(cb => cb.checked && !cb.disabled);
				const bar = document.getElementById('bulkActionsBar');
				const count = document.getElementById('selectedCount');
				const exportBtn = document.getElementById('exportBtn');
				const exportCount = document.getElementById('exportCount');
				const exportForm = document.getElementById('exportForm');
			
			// Get all selected IDs (including from sessionStorage)
			let allSelectedIds = selected.map(cb => parseInt(cb.value));
			const storedIds = sessionStorage.getItem('selectedUserIds');
			if (storedIds) {
				try {
					const storedSelectedIds = JSON.parse(storedIds);
					allSelectedIds = [...new Set([...allSelectedIds, ...storedSelectedIds])];
				} catch (e) {
					// Ignore parse errors
				}
			}
			
			// Update selected count display
			if (count) {
				count.textContent = allSelectedIds.length + ' selected';
			}
			
			// Export button will be updated in the section below
			
			// Update export form with selected user IDs
			if (exportForm) {
				// Remove all existing hidden inputs for users
				exportForm.querySelectorAll('input[name="users[]"]').forEach(input => {
					input.remove();
				});
				
				// Get all selected IDs (including from sessionStorage for "Select All Pages")
				let allSelectedIds = selected.map(cb => parseInt(cb.value));
				
				// Check if we have stored selection from "Select All Pages"
				const storedIds = sessionStorage.getItem('selectedUserIds');
				if (storedIds) {
					try {
						const storedSelectedIds = JSON.parse(storedIds);
						// If we have stored IDs from "Select All Pages", use those instead of merging
						// This ensures all users are included, not just visible ones
						if (storedSelectedIds.length > allSelectedIds.length) {
							allSelectedIds = storedSelectedIds;
						} else {
							// Merge with currently checked boxes
							allSelectedIds = [...new Set([...allSelectedIds, ...storedSelectedIds])];
						}
						console.log('Using stored IDs from sessionStorage:', allSelectedIds.length);
					} catch (e) {
						console.error('Error parsing stored IDs:', e);
					}
				}
				
				console.log('Total selected IDs for export:', allSelectedIds.length);
				
				// Add selected user IDs as hidden inputs
				allSelectedIds.forEach(userId => {
					const input = document.createElement('input');
					input.type = 'hidden';
					input.name = 'users[]';
					input.value = userId;
					exportForm.appendChild(input);
				});
				
				// Update export count to show total selected
				if (exportCount) {
					exportCount.textContent = allSelectedIds.length;
				}
				if (exportBtn) {
					exportBtn.disabled = allSelectedIds.length === 0;
				}
			}
			
				// Update bulk actions bar
				if (selected.length > 0 || allSelectedIds.length > 0) {
					if (bar) bar.classList.add('show');
				} else {
					if (bar) bar.classList.remove('show');
				}
			}

			// Bulk Status Update
			const bulkStatusBtn = document.getElementById('bulkStatusBtn');
			if (bulkStatusBtn) {
				bulkStatusBtn.addEventListener('click', function() {
			const status = document.getElementById('bulkStatusSelect').value;
			if (!status) {
				alert('Please select a status');
				return;
			}

			const selected = Array.from(userCheckboxes)
				.filter(cb => cb.checked && !cb.disabled)
				.map(cb => cb.value);

			if (selected.length === 0) {
				alert('Please select at least one user');
				return;
			}

			if (!confirm(`Update status to "${status}" for ${selected.length} user(s)?`)) {
				return;
			}

			fetch('{{ route("dashboard.user.bulk-status") }}', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': '{{ csrf_token() }}',
					'Accept': 'application/json'
				},
				body: JSON.stringify({
					user_ids: selected,
					status: status
				})
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					alert(data.message);
					location.reload();
				} else {
					alert('Error: ' + (data.message || 'Failed to update status'));
				}
			})
				.catch(error => {
					console.error('Error:', error);
					alert('An error occurred. Please try again.');
				});
			});
			}

			// Bulk Delete
			const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
			if (bulkDeleteBtn) {
				bulkDeleteBtn.addEventListener('click', function() {
			const selected = Array.from(userCheckboxes)
				.filter(cb => cb.checked && !cb.disabled)
				.map(cb => cb.value);

			if (selected.length === 0) {
				alert('Please select at least one user');
				return;
			}

			if (!confirm(`Are you sure you want to delete ${selected.length} user(s)? This action cannot be undone.`)) {
				return;
			}

			fetch('{{ route("dashboard.user.bulk-delete") }}', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': '{{ csrf_token() }}',
					'Accept': 'application/json'
				},
				body: JSON.stringify({
					user_ids: selected
				})
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					alert(data.message);
					location.reload();
				} else {
					alert('Error: ' + (data.message || 'Failed to delete users'));
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert('An error occurred. Please try again.');
			});
		});

			// Clear Selection
			const clearSelectionBtn = document.getElementById('clearSelectionBtn');
			if (clearSelectionBtn) {
				clearSelectionBtn.addEventListener('click', function() {
					userCheckboxes.forEach(cb => cb.checked = false);
					if (selectAll) selectAll.checked = false;
					sessionStorage.removeItem('selectedUserIds');
					updateBulkActionsBar();
				});
			}

			// Select All Users Across All Pages
			const selectAllPagesBtn = document.getElementById('selectAllPagesBtn');
			if (selectAllPagesBtn) {
				selectAllPagesBtn.addEventListener('click', function() {
				const totalUsers = {{ $unpaginatedusers->count() }};
				
				if (totalUsers === 0) {
					alert('No users to select.');
					return;
				}
				
				if (!confirm(`This will select all ${totalUsers} users across all pages. Continue?`)) {
					return;
				}
				
				// Get all user IDs from unpaginated users
				const allUserIds = [
					@foreach($unpaginatedusers as $user)
						{{ $user->id }},
					@endforeach
				].filter(id => id !== null && id !== undefined); // Remove any null/undefined values
				
				console.log('Selecting all users:', allUserIds.length, 'user IDs');
				
				// Check all checkboxes on current page that match
				userCheckboxes.forEach(cb => {
					if (allUserIds.includes(parseInt(cb.value)) && !cb.disabled) {
						cb.checked = true;
					}
				});
				
				// Store selected IDs in sessionStorage to persist across pages
				sessionStorage.setItem('selectedUserIds', JSON.stringify(allUserIds));
				console.log('Stored in sessionStorage:', allUserIds.length, 'user IDs');
				
				// Update UI
				if (selectAll) {
					// Check if all visible checkboxes are checked
					const allVisibleChecked = Array.from(userCheckboxes).every(cb => 
						cb.disabled || cb.checked || !allUserIds.includes(parseInt(cb.value))
					);
					selectAll.checked = allVisibleChecked;
				}
				
				updateBulkActionsBar();
				
				alert(`Selected all ${totalUsers} users. You can now export them as CSV.`);
				});
			} else {
				console.error('Select All Pages button not found!');
			}

			// Restore selection from sessionStorage on page load
			const storedIds = sessionStorage.getItem('selectedUserIds');
			if (storedIds) {
				try {
					const selectedIds = JSON.parse(storedIds);
					userCheckboxes.forEach(cb => {
						if (selectedIds.includes(parseInt(cb.value)) && !cb.disabled) {
							cb.checked = true;
						}
					});
					updateBulkActionsBar();
				} catch (e) {
					console.error('Error restoring selection:', e);
				}
			}
		}

		// Initialize when DOM is ready
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', initUserSelection);
		} else {
			// DOM already loaded
			initUserSelection();
		}

		// Auto-update status (existing functionality)
		document.querySelectorAll('.status-autoupate').forEach(select => {
			select.addEventListener('change', function() {
				const form = this.closest('form');
				if (form) {
					fetch(form.action, {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}',
							'Accept': 'application/json'
						},
						body: JSON.stringify({
							status: this.value
						})
					})
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							// Show success message
							const badge = this.closest('td').querySelector('.status-badge');
							if (badge) {
								badge.textContent = this.options[this.selectedIndex].text;
								badge.className = 'status-badge ' + this.value;
							}
						}
					})
					.catch(error => {
						console.error('Error:', error);
						alert('Failed to update status. Please try again.');
						this.form.submit(); // Fallback to form submission
					});
				}
			});
		});
		} // End of initUserSelection
	})();
</script>
@endpush
@endsection
