@extends('admin.template.layout')

@section('content')
	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<div>
			<h1 class="h3 mb-0 text-gray-800">Admin Dashboard</h1>
			<p class="text-muted mb-0">Overview of all platform activities and statistics</p>
		</div>
		<div>
			<a href="{{ route('dashboard.timesheets.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm mr-2">
				<i class="fa fa-clock-o fa-sm text-white-50"></i> Manage Timesheets
			</a>
			<a href="{{ route('dashboard.job-applications.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm mr-2">
				<i class="fa fa-file-text fa-sm text-white-50"></i> Manage Applications
			</a>
			<a href="{{ route('dashboard') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
				<i class="fa fa-sync fa-sm text-white-50"></i> Refresh
			</a>
		</div>
	</div>

	@if(session('success'))
		<div class="alert alert-success alert-dismissible fade show">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
	@endif

	<!-- Quick Stats Overview -->
	<div class="row mb-4">
		<!-- Timesheets Section -->
		<div class="col-12 mb-3">
			<h5 class="text-primary"><i class="fa fa-clock-o"></i> Timesheets Overview</h5>
		</div>
		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-primary shadow h-100 py-2">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Timesheets</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalTimesheets) }}</div>
							<small class="text-muted">Today: {{ $todayTimesheets }}</small>
						</div>
						<div class="col-auto">
							<i class="fa fa-clock-o fa-2x text-gray-300"></i>
						</div>
					</div>
					<a href="{{ route('dashboard.timesheets.index') }}" class="stretched-link"></a>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-warning shadow h-100 py-2">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Approval</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($pendingTimesheets) }}</div>
							<small class="text-muted">Requires action</small>
						</div>
						<div class="col-auto">
							<i class="fa fa-hourglass-half fa-2x text-gray-300"></i>
						</div>
					</div>
					<a href="{{ route('dashboard.timesheets.index', ['status' => 'pending']) }}" class="stretched-link"></a>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-success shadow h-100 py-2">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-success text-uppercase mb-1">Approved</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($approvedTimesheets) }}</div>
							<small class="text-muted">This month: £{{ number_format($thisMonthAmount, 2) }}</small>
						</div>
						<div class="col-auto">
							<i class="fa fa-check-circle fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-info shadow h-100 py-2">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Revenue</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">£{{ number_format($totalAmount, 2) }}</div>
							<small class="text-muted">All approved</small>
						</div>
						<div class="col-auto">
							<i class="fa fa-pound fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Users & Jobs Overview -->
	<div class="row mb-4">
		<div class="col-12 mb-3">
			<h5 class="text-primary"><i class="fa fa-users"></i> Users & Jobs Overview</h5>
		</div>
		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-primary shadow h-100 py-2">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalUsers) }}</div>
							<small class="text-muted">Active: {{ number_format($activeUsers) }}</small>
						</div>
						<div class="col-auto">
							<i class="fa fa-users fa-2x text-gray-300"></i>
						</div>
					</div>
					<a href="{{ route('dashboard.user.index') }}" class="stretched-link"></a>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-success shadow h-100 py-2">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-success text-uppercase mb-1">Service Providers</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalCarers) }}</div>
							<small class="text-muted">
								Carers: {{ $totalCarersOnly }} | 
								Childminders: {{ $totalChildminders }} | 
								Housekeepers: {{ $totalHousekeepers }}
							</small>
						</div>
						<div class="col-auto">
							<i class="fa fa-user-md fa-2x text-gray-300"></i>
						</div>
					</div>
					<a href="{{ route('dashboard.user.index', ['role' => 'carer']) }}" class="stretched-link"></a>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-info shadow h-100 py-2">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-info text-uppercase mb-1">Clients</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalClients) }}</div>
							<small class="text-muted">Active users</small>
						</div>
						<div class="col-auto">
							<i class="fa fa-user fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-warning shadow h-100 py-2">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Active Jobs</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($activeJobs) }}</div>
							<small class="text-muted">
								Carers: {{ $jobsForCarers }} | 
								Childminders: {{ $jobsForChildminders }} | 
								Housekeepers: {{ $jobsForHousekeepers }}
							</small>
						</div>
						<div class="col-auto">
							<i class="fa fa-briefcase fa-2x text-gray-300"></i>
						</div>
					</div>
					<a href="{{ route('dashboard.contract.index') }}" class="stretched-link"></a>
				</div>
			</div>
		</div>
	</div>

	<!-- Applications & Activity -->
	<div class="row mb-4">
		<div class="col-12 mb-3">
			<h5 class="text-primary"><i class="fa fa-file-text"></i> Applications & Activity</h5>
		</div>
		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-primary shadow h-100 py-2">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Applications</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalApplications) }}</div>
							<small class="text-muted">This week: {{ $applicationsThisWeek }}</small>
						</div>
						<div class="col-auto">
							<i class="fa fa-file-text fa-2x text-gray-300"></i>
						</div>
					</div>
					<a href="{{ route('dashboard.job-applications.index') }}" class="stretched-link"></a>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-warning shadow h-100 py-2">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Applications</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($pendingApplications) }}</div>
							<small class="text-muted">Awaiting review</small>
						</div>
						<div class="col-auto">
							<i class="fa fa-hourglass-half fa-2x text-gray-300"></i>
						</div>
					</div>
					<a href="{{ route('dashboard.job-applications.index', ['status' => 'pending']) }}" class="stretched-link"></a>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-success shadow h-100 py-2">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-success text-uppercase mb-1">Accepted</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($acceptedApplications) }}</div>
							<small class="text-muted">Successful matches</small>
						</div>
						<div class="col-auto">
							<i class="fa fa-check-circle fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-danger shadow h-100 py-2">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Unread Notifications</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($unreadNotifications) }}</div>
							<small class="text-muted">Today: {{ $notificationsToday }}</small>
						</div>
						<div class="col-auto">
							<i class="fa fa-bell fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Timesheets Preview Section -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="card shadow mb-4">
				<div class="card-header py-3 d-flex justify-content-between align-items-center">
					<h6 class="m-0 font-weight-bold text-primary">
						<i class="fa fa-clock-o"></i> Recent Timesheets
					</h6>
					<a href="{{ route('dashboard.timesheets.index') }}" class="btn btn-sm btn-primary">
						<i class="fa fa-arrow-right"></i> View All & Manage
					</a>
				</div>
				<div class="card-body">
					<!-- Timesheets Preview Table -->
					<div class="table-responsive">
						<table class="table table-bordered table-hover table-sm" width="100%" cellspacing="0">
							<thead class="thead-light">
								<tr>
									<th>ID</th>
									<th>Date</th>
									<th>Carer</th>
									<th>Client</th>
									<th>Job</th>
									<th>Hours</th>
									<th>Amount</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								@forelse($timesheets as $timesheet)
									<tr style="cursor: pointer;" onclick="window.location='{{ route('dashboard.timesheets.show', $timesheet->id) }}'">
										<td>{{ $timesheet->id }}</td>
										<td>
											@if(isset($timesheet->date) && $timesheet->date)
												{{ $timesheet->date->format('d M Y') }}
											@elseif($timesheet->clock_in)
												{{ $timesheet->clock_in->format('d M Y') }}
											@else
												{{ $timesheet->created_at->format('d M Y') }}
											@endif
										</td>
										<td>
											@if($timesheet->carer)
												{{ $timesheet->carer->firstname }} {{ $timesheet->carer->lastname ?? '' }}
											@else
												<span class="text-muted">N/A</span>
											@endif
										</td>
										<td>
											@if($timesheet->client)
												{{ $timesheet->client->firstname }} {{ substr($timesheet->client->lastname ?? '', 0, 1) }}.
											@else
												<span class="text-muted">N/A</span>
											@endif
										</td>
										<td>{{ Str::limit($timesheet->contract->title ?? 'N/A', 25) }}</td>
										<td>{{ $timesheet->hours_worked ? number_format($timesheet->hours_worked, 2) . 'h' : '-' }}</td>
										<td>
											@if($timesheet->total_amount)
												<strong class="text-success">£{{ number_format($timesheet->total_amount, 2) }}</strong>
											@else
												-
											@endif
										</td>
										<td>
											<span class="badge 
												@if($timesheet->status == 'pending') badge-warning
												@elseif($timesheet->status == 'approved') badge-success
												@elseif($timesheet->status == 'disputed') badge-danger
												@else badge-secondary
												@endif">
												{{ ucfirst($timesheet->status ?? 'pending') }}
											</span>
										</td>
									</tr>
								@empty
									<tr>
										<td colspan="8" class="text-center text-muted py-4">No timesheets found</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>
					<div class="text-center mt-3">
						<small class="text-muted">Showing {{ $timesheets->count() }} recent timesheets</small>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Recent Activity Section -->
	<div class="row">
		<!-- Recent Applications -->
		<div class="col-xl-6 col-lg-6 mb-4">
			<div class="card shadow mb-4">
				<div class="card-header py-3 d-flex justify-content-between align-items-center">
					<h6 class="m-0 font-weight-bold text-primary">
						<i class="fa fa-file-text"></i> Recent Job Applications
					</h6>
					<a href="{{ route('dashboard.job-applications.index') }}" class="btn btn-sm btn-outline-primary">
						View All
					</a>
				</div>
				<div class="card-body">
					@if($recentApplications->count() > 0)
						<div class="list-group">
							@foreach($recentApplications as $application)
								<a href="{{ route('dashboard.job-applications.show', $application->id) }}" class="list-group-item list-group-item-action">
									<div class="d-flex justify-content-between align-items-center">
										<div>
											<strong>{{ $application->carer->firstname ?? 'N/A' }} {{ substr($application->carer->lastname ?? '', 0, 1) }}.</strong> applied to
											<strong>{{ Str::limit($application->contract->title ?? 'Job #' . $application->contract_id, 30) }}</strong>
											<br>
											<small class="text-muted">
												<i class="fa fa-clock-o"></i> {{ $application->created_at->diffForHumans() }}
												@if($application->proposed_rate)
													| <i class="fa fa-pound"></i> £{{ number_format($application->proposed_rate, 2) }}/hr
												@endif
											</small>
										</div>
										<span class="badge badge-{{ $application->status == 'accepted' ? 'success' : ($application->status == 'rejected' ? 'danger' : 'warning') }}">
											{{ ucfirst($application->status) }}
										</span>
									</div>
								</a>
							@endforeach
						</div>
					@else
						<p class="text-muted text-center py-3">No recent applications</p>
					@endif
				</div>
			</div>
		</div>

		<!-- Recent Jobs -->
		<div class="col-xl-6 col-lg-6 mb-4">
			<div class="card shadow mb-4">
				<div class="card-header py-3 d-flex justify-content-between align-items-center">
					<h6 class="m-0 font-weight-bold text-primary">
						<i class="fa fa-briefcase"></i> Recent Jobs Posted
					</h6>
					<a href="{{ route('dashboard.contract.index') }}" class="btn btn-sm btn-outline-primary">
						View All
					</a>
				</div>
				<div class="card-body">
					@if($recentJobs->count() > 0)
						<div class="list-group">
							@foreach($recentJobs as $job)
								<a href="{{ route('dashboard.contract.show', $job->id) }}" class="list-group-item list-group-item-action">
									<div class="d-flex justify-content-between align-items-center">
										<div>
											<strong>{{ Str::limit($job->title ?? 'Untitled Job', 40) }}</strong>
											<br>
											<small class="text-muted">
												<i class="fa fa-user"></i> {{ $job->user->firstname ?? 'N/A' }} {{ substr($job->user->lastname ?? '', 0, 1) }}.
												| <i class="fa fa-clock-o"></i> {{ $job->created_at->diffForHumans() }}
											</small>
										</div>
										<span class="badge badge-{{ $job->status == 'active' ? 'success' : 'secondary' }}">
											{{ ucfirst($job->status ?? 'pending') }}
										</span>
									</div>
								</a>
							@endforeach
						</div>
					@else
						<p class="text-muted text-center py-3">No recent jobs</p>
					@endif
				</div>
			</div>
		</div>
	</div>

@endsection
