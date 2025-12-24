@extends('admin.template.layout')

@section('content')
<style>
    .application-detail-container {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 20px 0;
    }
    
    .application-header-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 30px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }
    
    .status-badge-large {
        display: inline-block;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
    }
    
    .status-pending {
        background: rgba(255, 193, 7, 0.2);
        color: #ffc107;
        border: 2px solid #ffc107;
    }
    
    .status-accepted {
        background: rgba(40, 167, 69, 0.2);
        color: #28a745;
        border: 2px solid #28a745;
    }
    
    .status-rejected {
        background: rgba(220, 53, 69, 0.2);
        color: #dc3545;
        border: 2px solid #dc3545;
    }
    
    .info-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }
    
    .info-section-title {
        font-size: 16px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .info-section-title i {
        color: #667eea;
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f1f3f5;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 14px;
    }
    
    .info-value {
        color: #2d3748;
        font-size: 14px;
        text-align: right;
    }
    
    .cover-letter-box {
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        padding: 20px;
        border-radius: 8px;
        margin-top: 15px;
    }
    
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }
    
    .btn-modern {
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-success-modern {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
    }
    
    .btn-success-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .btn-danger-modern {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
    }
    
    .btn-danger-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .btn-secondary-modern {
        background: #e2e8f0;
        color: #4a5568;
    }
    
    .btn-secondary-modern:hover {
        background: #cbd5e0;
        color: #4a5568;
        text-decoration: none;
    }
    
    .carer-profile-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }
    
    .carer-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #e9ecef;
        margin: 0 auto 20px;
        display: block;
    }
    
    .job-details-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }
</style>

<div class="application-detail-container">
    <!-- Application Header -->
    <div class="application-header-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap">
            <div>
                <h2 class="mb-3">Job Application Details</h2>
                <p class="mb-2"><i class="fa fa-calendar"></i> Applied: {{ $application->created_at->format('F d, Y \a\t g:i A') }}</p>
                <p class="mb-0"><i class="fa fa-clock-o"></i> {{ $application->created_at->diffForHumans() }}</p>
            </div>
            <div class="text-right">
                <div class="status-badge-large status-{{ $application->status }}">
                    {{ ucfirst($application->status) }}
                </div>
                <div class="mt-3">
                    <a href="{{ route('dashboard.job-applications.index') }}" class="btn btn-secondary-modern btn-modern">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Carer & Application Info -->
        <div class="col-lg-8">
            <!-- Carer Profile Card -->
            @if($application->carer)
            <div class="carer-profile-card mb-4">
                <div class="info-section-title">
                    <i class="fa fa-user"></i>
                    <span>Service Provider Information</span>
                </div>
                <div class="text-center mb-4">
                    <img src="{{ asset($application->carer->profile ?? 'img/default-avatar.png') }}" 
                         alt="{{ $application->carer->firstname }} {{ $application->carer->lastname }}"
                         class="carer-avatar">
                    <h4 class="mb-1">{{ $application->carer->firstname }} {{ $application->carer->lastname }}</h4>
                    <p class="text-muted mb-2">{{ $application->carer->role->title ?? 'Service Provider' }}</p>
                    <div class="d-flex justify-content-center gap-3 mb-3">
                        @if($application->carer->approved)
                            <span class="badge badge-success"><i class="fa fa-check-circle"></i> Verified</span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <span class="info-label"><i class="fa fa-envelope"></i> Email</span>
                            <span class="info-value">{{ $application->carer->email }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <span class="info-label"><i class="fa fa-phone"></i> Phone</span>
                            <span class="info-value">{{ $application->carer->phone ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <span class="info-label"><i class="fa fa-map-marker"></i> Location</span>
                            <span class="info-value">{{ $application->carer->city ?? 'N/A' }}, {{ $application->carer->country ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <span class="info-label"><i class="fa fa-star"></i> Status</span>
                            <span class="info-value">
                                <span class="badge badge-{{ $application->carer->status == 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst($application->carer->status ?? 'pending') }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('dashboard.user.edit', ['user' => $application->carer->id]) }}" 
                       class="btn btn-secondary-modern btn-modern">
                        <i class="fa fa-edit"></i> View Full Profile
                    </a>
                    <a href="{{ route('seller.show', $application->carer->id) }}" 
                       target="_blank"
                       class="btn btn-secondary-modern btn-modern">
                        <i class="fa fa-eye"></i> Public Profile
                    </a>
                </div>
            </div>
            @endif

            <!-- Application Details Card -->
            <div class="info-card">
                <div class="info-section-title">
                    <i class="fa fa-file-text"></i>
                    <span>Application Details</span>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <span class="info-label">Application ID</span>
                            <span class="info-value">#{{ $application->id }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <span class="info-label">Status</span>
                            <span class="info-value">
                                <span class="badge badge-{{ $application->status == 'pending' ? 'warning' : ($application->status == 'accepted' ? 'success' : 'danger') }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </span>
                        </div>
                    </div>
                    @if($application->proposed_rate)
                    <div class="col-md-6">
                        <div class="info-item">
                            <span class="info-label"><i class="fa fa-pound"></i> Proposed Rate</span>
                            <span class="info-value"><strong>£{{ number_format($application->proposed_rate, 2) }}/hour</strong></span>
                        </div>
                    </div>
                    @endif
                    @if($application->responded_at)
                    <div class="col-md-6">
                        <div class="info-item">
                            <span class="info-label">Responded At</span>
                            <span class="info-value">{{ $application->responded_at->format('M d, Y g:i A') }}</span>
                        </div>
                    </div>
                    @endif
                    @if($application->rejection_reason)
                    <div class="col-12">
                        <div class="info-item">
                            <span class="info-label">Rejection Reason</span>
                            <span class="info-value">{{ $application->rejection_reason }}</span>
                        </div>
                    </div>
                    @endif
                </div>

                @if($application->cover_letter)
                <div class="cover-letter-box">
                    <div class="info-section-title" style="border-bottom: none; margin-bottom: 10px; padding-bottom: 0;">
                        <i class="fa fa-envelope-open"></i>
                        <span>Cover Letter</span>
                    </div>
                    <p class="mb-0" style="line-height: 1.8; color: #4a5568;">{{ $application->cover_letter }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Job Details & Actions -->
        <div class="col-lg-4">
            <!-- Job Details Card -->
            @if($application->contract)
            <div class="job-details-card mb-4">
                <div class="info-section-title">
                    <i class="fa fa-briefcase"></i>
                    <span>Job Details</span>
                </div>
                <h5 class="mb-3">{{ $application->contract->title }}</h5>
                <div class="info-item">
                    <span class="info-label">Job ID</span>
                    <span class="info-value">#{{ $application->contract->id }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Client</span>
                    <span class="info-value">
                        @if($application->contract->user)
                            {{ $application->contract->user->firstname }} {{ substr($application->contract->user->lastname ?? '', 0, 1) }}.
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                @if($application->contract->hourly_rate || $application->contract->daily_rate || $application->contract->weekly_rate)
                @php
                    $pricingBreakdown = $application->contract->getPricingBreakdown();
                @endphp
                <div class="info-item">
                    <span class="info-label">Pricing Breakdown</span>
                    <span class="info-value">
                        <div class="pricing-details">
                            <div><strong>Client Posted:</strong> £{{ number_format($pricingBreakdown['client_rate'], 2) }}/{{ $pricingBreakdown['type'] }}</div>
                            <div class="text-success"><strong>Provider Receives (66.6%):</strong> £{{ number_format($pricingBreakdown['provider_rate'], 2) }}/{{ $pricingBreakdown['type'] }}</div>
                            <div class="text-info"><strong>Platform Fee (33.3333%):</strong> £{{ number_format($pricingBreakdown['platform_fee'], 2) }}/{{ $pricingBreakdown['type'] }}</div>
                        </div>
                    </span>
                </div>
                @endif
                @if($application->contract->address)
                <div class="info-item">
                    <span class="info-label">Location</span>
                    <span class="info-value">{{ Str::limit($application->contract->address, 30) }}</span>
                </div>
                @endif
                <div class="info-item">
                    <span class="info-label">Status</span>
                    <span class="info-value">
                        <span class="badge badge-{{ $application->contract->status == 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($application->contract->status ?? 'pending') }}
                        </span>
                    </span>
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('dashboard.contract.show', $application->contract->id) }}" 
                       class="btn btn-secondary-modern btn-modern">
                        <i class="fa fa-eye"></i> View Job
                    </a>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            @if($application->status == 'pending')
            <div class="info-card">
                <div class="info-section-title">
                    <i class="fa fa-cog"></i>
                    <span>Actions</span>
                </div>
                <form action="{{ route('job-applications.accept', $application->id) }}" method="POST" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-success-modern btn-modern w-100" 
                            onclick="return confirm('Are you sure you want to accept this application?')">
                        <i class="fa fa-check-circle"></i> Accept Application
                    </button>
                </form>
                <form action="{{ route('job-applications.reject', $application->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger-modern btn-modern w-100"
                            onclick="return confirm('Are you sure you want to reject this application?')">
                        <i class="fa fa-times-circle"></i> Reject Application
                    </button>
                </form>
            </div>
            @elseif($application->status == 'accepted')
            <div class="info-card">
                <div class="alert alert-success">
                    <i class="fa fa-check-circle"></i> This application has been accepted.
                </div>
                @if($application->carer)
                <a href="{{ route('dashboard.user.edit', ['user' => $application->carer->id]) }}" 
                   class="btn btn-secondary-modern btn-modern w-100">
                    <i class="fa fa-user"></i> View Carer Profile
                </a>
                @endif
            </div>
            @elseif($application->status == 'rejected')
            <div class="info-card">
                <div class="alert alert-danger">
                    <i class="fa fa-times-circle"></i> This application has been rejected.
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

