@extends('admin.template.layout')

@section('content')
<style>
    .user-edit-container {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 20px 0;
    }
    
    .user-header-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 30px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }
    
    .user-avatar-section {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    
    .user-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid rgba(255, 255, 255, 0.3);
        object-fit: cover;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .user-avatar:hover {
        transform: scale(1.05);
        border-color: rgba(255, 255, 255, 0.6);
    }
    
    .user-info h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
    }
    
    .user-info p {
        margin: 5px 0;
        opacity: 0.9;
        font-size: 14px;
    }
    
    .user-badges {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        flex-wrap: wrap;
    }
    
    .badge-item {
        background: rgba(255, 255, 255, 0.2);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        backdrop-filter: blur(10px);
    }
    
    .nav-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .nav-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .nav-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }
    
    .main-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    
    .tab-navigation {
        background: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
        padding: 0;
        display: flex;
        flex-wrap: wrap;
    }
    
    .tab-item {
        padding: 20px 30px;
        cursor: pointer;
        border: none;
        background: transparent;
        font-weight: 600;
        color: #6c757d;
        position: relative;
        transition: all 0.3s ease;
        font-size: 14px;
    }
    
    .tab-item:hover {
        color: #667eea;
        background: rgba(102, 126, 234, 0.05);
    }
    
    .tab-item.active {
        color: #667eea;
        background: white;
    }
    
    .tab-item.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 3px;
        background: #667eea;
    }
    
    .tab-content {
        padding: 40px;
        display: none;
    }
    
    .tab-content.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .form-section {
        margin-bottom: 40px;
    }
    
    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .section-title i {
        color: #667eea;
    }
    
    .form-group-modern {
        margin-bottom: 25px;
    }
    
    .form-label-modern {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 8px;
        display: block;
        font-size: 14px;
    }
    
    .form-control-modern {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .form-control-modern:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .badge-toggle {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-right: 10px;
        margin-bottom: 10px;
    }
    
    .badge-toggle:hover {
        border-color: #667eea;
        background: #f7faff;
    }
    
    .badge-toggle input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    
    .badge-toggle.active {
        border-color: #667eea;
        background: #667eea;
        color: white;
    }
    
    .badge-toggle.active label {
        color: white;
    }
    
    .submit-section {
        background: #f8f9fa;
        padding: 30px;
        border-top: 2px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .btn-modern {
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
    }
    
    .btn-primary-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    
    .btn-secondary-modern {
        background: #e2e8f0;
        color: #4a5568;
    }
    
    .btn-secondary-modern:hover {
        background: #cbd5e0;
    }
    
    .upload-area {
        border: 2px dashed #cbd5e0;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }
    
    .upload-area:hover {
        border-color: #667eea;
        background: #f7faff;
    }
    
    .upload-area img {
        max-width: 200px;
        max-height: 200px;
        border-radius: 12px;
        margin-bottom: 15px;
    }
    
    .status-indicator {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .status-active {
        background: #c6f6d5;
        color: #22543d;
    }
    
    .status-pending {
        background: #fed7d7;
        color: #742a2a;
    }
    
    .status-block {
        background: #e2e8f0;
        color: #2d3748;
    }
    
    @media (max-width: 768px) {
        .user-header-card {
            padding: 20px;
        }
        
        .user-avatar-section {
            flex-direction: column;
            text-align: center;
        }
        
        .tab-navigation {
            overflow-x: auto;
        }
        
        .tab-item {
            padding: 15px 20px;
            white-space: nowrap;
        }
        
        .tab-content {
            padding: 20px;
        }
    }
</style>

<div class="user-edit-container">
    <!-- User Header Card -->
    <div class="user-header-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap">
                <div class="user-avatar-section">
                <div class="upload-wrap" style="position: relative;">
                    <img class="user-avatar" src="{{ asset($profile->profile ?? 'img/default-avatar.png') }}" 
                         alt="{{ $profile->firstname }} {{ $profile->lastname }}"
                         onclick="document.querySelector('.upload-input').click()">
                    <input class="upload-input d-none" type="file" name="profile" accept="image/*">
                    @if($profile->profile_locked)
                        <div style="position: absolute; top: 5px; right: 5px; background: #f59e0b; color: white; padding: 5px 10px; border-radius: 5px; font-size: 12px;">
                            <i class="fa fa-lock"></i> Locked
                        </div>
                    @endif
                </div>
                <div class="user-info">
                    <h2>{{ $profile->firstname }} {{ $profile->lastname }}</h2>
                    <p><i class="fa fa-envelope"></i> {{ $profile->email }}</p>
                    <p><i class="fa fa-phone"></i> {{ $profile->phone ?? 'N/A' }}</p>
                    <div class="user-badges">
                        <span class="status-indicator status-{{ $profile->status ?? 'pending' }}">
                            {{ ucfirst($profile->status ?? 'pending') }}
                        </span>
                        @if($profile->role)
                            <span class="badge-item">{{ $profile->role->title }}</span>
                        @endif
                        @if($profile->approved)
                            <span class="badge-item"><i class="fa fa-check-circle"></i> Verified</span>
                        @endif
                        @if($profile->profile_verified_at)
                            <span class="badge-item" style="background: #10b981;">
                                <i class="fa fa-user-check"></i> Face Verified
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="nav-actions">
                @if (isset($previous))
                    <a href="{{ route('dashboard.user.edit', ['user' => $previous]) }}" class="nav-btn" title="Previous User">
                        <i class="fa fa-chevron-left"></i>
                    </a>
                @endif
                @if (isset($next))
                    <a href="{{ route('dashboard.user.edit', ['user' => $next]) }}" class="nav-btn" title="Next User">
                        <i class="fa fa-chevron-right"></i>
                    </a>
                @endif
                @if(!$profile->power_admin)
                    <button type="button" class="btn btn-warning btn-modern" data-toggle="modal" data-target="#resetPasswordModal" style="background: #f59e0b; color: white;">
                        <i class="fa fa-key"></i> Reset Password
                    </button>
                @endif
                <a href="{{ route('dashboard.user.index') }}" class="btn btn-secondary-modern btn-modern">
                    <i class="fa fa-list"></i> Manage All
                </a>
            </div>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="main-card">
        <!-- Tab Navigation -->
        <div class="tab-navigation">
            <button class="tab-item active" data-tab="basic">
                <i class="fa fa-user"></i> Basic Info
            </button>
            <button class="tab-item" data-tab="documents">
                <i class="fa fa-file"></i> Documents
            </button>
            <button class="tab-item" data-tab="languages">
                <i class="fa fa-language"></i> Languages
            </button>
            @if($profile->role_id > 2)
                <button class="tab-item" data-tab="services">
                    <i class="fa fa-briefcase"></i> Services & Experience
                </button>
                <button class="tab-item" data-tab="availability">
                    <i class="fa fa-calendar"></i> Availability
                </button>
                <button class="tab-item" data-tab="references">
                    <i class="fa fa-users"></i> References & DBS
                </button>
            @endif
            <button class="tab-item" data-tab="badges">
                <i class="fa fa-certificate"></i> Badges & Status
            </button>
        </div>

        <!-- Form -->
        <form action="{{ route('dashboard.user.update', ['user' => $profile->id]) }}" method="POST" enctype="multipart/form-data" id="userEditForm">
            @csrf
            @method('PUT')

            <!-- Basic Info Tab -->
            <div class="tab-content active" id="tab-basic">
                <div class="form-section">
                    <div class="section-title">
                        <i class="fa fa-user-circle"></i>
                        <span>Profile Information</span>
                    </div>
                    @include('app.pages.profile.video-verification', compact('profile'))
                    @include('app.pages.profile.basic', compact('profile'))
                </div>
            </div>

            <!-- Documents Tab -->
            <div class="tab-content" id="tab-documents">
                <div class="form-section">
                    <div class="section-title">
                        <i class="fa fa-file-text"></i>
                        <span>Documents</span>
                    </div>
                    @include('app.pages.profile.documents-list', compact('documents'))
                </div>
            </div>

            <!-- Languages Tab -->
            <div class="tab-content" id="tab-languages">
                <div class="form-section">
                    <div class="section-title">
                        <i class="fa fa-globe"></i>
                        <span>Languages & Communication</span>
                    </div>
                    @include('app.pages.profile.languages', compact('profile'))
                </div>
            </div>

            @if($profile->role_id > 2)
                <!-- Services & Experience Tab -->
                <div class="tab-content" id="tab-services">
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fa fa-briefcase"></i>
                            <span>Service Types</span>
                        </div>
                        @include('app.pages.profile.service-type', compact('profile'))
                    </div>
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fa fa-star"></i>
                            <span>Experience & Skills</span>
                        </div>
                        @include('app.pages.profile.years_experience', compact('profile'))
                        @include('app.pages.profile.experiences', compact('profile'))
                        @include('app.pages.profile.skills', compact('profile'))
                        @include('app.pages.profile.interests', compact('profile'))
                        @include('app.pages.profile.educations', compact('profile'))
                    </div>
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fa fa-info-circle"></i>
                            <span>Additional Information</span>
                        </div>
                        @include('app.pages.profile.infos', compact('profile'))
                    </div>
                    @if($profile->role_id == 5)
                        <div class="form-section">
                            <div class="section-title">
                                <i class="fa fa-pound-sign"></i>
                                <span>Pricing</span>
                            </div>
                            @include('app.pages.profile.childminder-fee', compact('profile'))
                        </div>
                    @endif
                </div>

                <!-- Availability Tab -->
                <div class="tab-content" id="tab-availability">
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fa fa-calendar-check"></i>
                            <span>Working Schedule</span>
                        </div>
                        @include('app.pages.profile.working-days', compact('profile'))
                        @include('app.pages.profile.working-time', compact('profile'))
                    </div>
                </div>

                <!-- References & DBS Tab -->
                <div class="tab-content" id="tab-references">
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fa fa-users"></i>
                            <span>References</span>
                        </div>
                        @include('app.pages.profile.references', compact('profile'))
                    </div>
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fa fa-shield-alt"></i>
                            <span>DBS & Certifications</span>
                        </div>
                        @include('app.pages.profile.dbs', compact('profile'))
                    </div>
                </div>
            @endif

            <!-- Badges & Status Tab -->
            <div class="tab-content" id="tab-badges">
                <div class="form-section">
                    <div class="section-title">
                        <i class="fa fa-certificate"></i>
                        <span>User Badges & Verification</span>
                    </div>
                    
                    <!-- Profile Photo Lock Section -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fa fa-lock"></i> Profile Photo Lock</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="profile_locked" id="profile_locked" 
                                               value="1" {{ $profile->profile_locked ? 'checked' : '' }}>
                                        <label class="form-check-label" for="profile_locked">
                                            <strong>Lock Profile Photo</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        When locked, users cannot change their profile photo. This is useful after face verification.
                                    </small>
                                    @if($profile->profile_verified_at)
                                        <div class="alert alert-success mt-3">
                                            <i class="fa fa-check-circle"></i> 
                                            <strong>Face Verified:</strong> {{ $profile->profile_verified_at->format('d M Y H:i') }}
                                            @if($profile->profile_verification_id)
                                                <br><small>Verification ID: {{ $profile->profile_verification_id }}</small>
                                            @endif
                                        </div>
                                    @elseif($profile->profile_locked)
                                        <div class="alert alert-warning mt-3">
                                            <i class="fa fa-exclamation-triangle"></i> 
                                            Profile is locked but not face verified yet.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="badge-toggle @if($profile->approved) active @endif">
                                <input type="checkbox" name="approved" value="1" 
                                       @if($profile->approved) checked @endif>
                                <i class="fa fa-check-circle"></i>
                                <span>Verified & Approved</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Section -->
            <div class="submit-section">
                <a href="{{ route('dashboard.user.index') }}" class="btn btn-secondary-modern btn-modern">
                    <i class="fa fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary-modern btn-modern">
                    <i class="fa fa-save"></i> Update User Profile
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reset Password Modal -->
@if(!$profile->power_admin)
<div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordModalLabel">
                    <i class="fa fa-key"></i> Reset Password for {{ $profile->firstname }} {{ $profile->lastname }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="resetPasswordForm" action="{{ route('dashboard.user.reset-password', ['user' => $profile->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> 
                        This will generate a temporary password and send it to <strong>{{ $profile->email }}</strong>. 
                        The user will need to:
                        <ul class="mb-0 mt-2">
                            <li>Log in with the temporary password</li>
                            <li>Create a new secure password</li>
                            <li>Verify their phone number</li>
                        </ul>
                    </div>
                    <div id="passwordResetError" class="alert alert-danger" style="display: none;"></div>
                    <div id="passwordResetSuccess" class="alert alert-success" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" style="background: #f59e0b; color: white;">
                        <i class="fa fa-key"></i> Generate & Send Temporary Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
    // Tab Navigation
    document.querySelectorAll('.tab-item').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and contents
            document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            document.getElementById('tab-' + tabName).classList.add('active');
        });
    });

    // Badge toggle styling
    document.querySelectorAll('.badge-toggle input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                this.closest('.badge-toggle').classList.add('active');
            } else {
                this.closest('.badge-toggle').classList.remove('active');
            }
        });
    });

    // Image preview on upload
    document.querySelector('.upload-input')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('.user-avatar').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Password Reset Form Handler
    @if(!$profile->power_admin)
    document.getElementById('resetPasswordForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const errorDiv = document.getElementById('passwordResetError');
        const successDiv = document.getElementById('passwordResetSuccess');
        
        // Hide previous messages
        errorDiv.style.display = 'none';
        successDiv.style.display = 'none';
        
        // Submit via AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let message = data.message || 'Temporary password generated and sent successfully!';
                
                // If email failed but password was generated, show the password
                if (data.warning && data.temporary_password) {
                    message += '<br><br><strong>⚠️ Email Failed - Temporary Password:</strong><br>';
                    message += '<div style="background: #fff3cd; border: 2px dashed #856404; padding: 15px; margin-top: 10px; border-radius: 5px; font-family: monospace; font-size: 18px; font-weight: bold; text-align: center; color: #856404;">' + data.temporary_password + '</div>';
                    message += '<br><small>Please copy this password and share it with ' + (data.user_email || 'the user') + ' manually.</small>';
                }
                
                successDiv.innerHTML = message;
                successDiv.style.display = 'block';
                
                // Don't auto-close if email failed (so admin can copy password)
                if (!data.warning) {
                    // Close modal after 3 seconds if email sent successfully
                    setTimeout(function() {
                        const modal = document.getElementById('resetPasswordModal');
                        if (modal) {
                            // Use Bootstrap modal API
                            if (typeof bootstrap !== 'undefined') {
                                const bsModal = bootstrap.Modal.getInstance(modal);
                                if (bsModal) bsModal.hide();
                            } else if (typeof $ !== 'undefined') {
                                $(modal).modal('hide');
                            } else {
                                modal.style.display = 'none';
                                document.body.classList.remove('modal-open');
                            }
                        }
                        successDiv.style.display = 'none';
                    }, 5000);
                }
            } else {
                errorDiv.textContent = data.message || 'An error occurred while generating the temporary password.';
                errorDiv.style.display = 'block';
            }
        })
        .catch(error => {
            errorDiv.textContent = 'An error occurred. Please try again.';
            errorDiv.style.display = 'block';
            console.error('Error:', error);
        });
    });
    
    // Reset form when modal is closed
    const resetPasswordModal = document.getElementById('resetPasswordModal');
    if (resetPasswordModal) {
        resetPasswordModal.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('resetPasswordForm');
            const errorDiv = document.getElementById('passwordResetError');
            const successDiv = document.getElementById('passwordResetSuccess');
            if (form) form.reset();
            if (errorDiv) errorDiv.style.display = 'none';
            if (successDiv) successDiv.style.display = 'none';
        });
    }
    @endif
</script>
@endsection
