@extends('app.template.auth-layout')

@section('content')
<style>
	:root {
		--role-color-carer: #4CAF50;
		--role-color-housekeeper: #2196F3;
		--role-color-childminder: #FF9800;
		--role-color-client: #9C27B0;
	}
	
	.registration-page {
		min-height: calc(100vh - 200px);
		padding: 40px 0;
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		position: relative;
		overflow: hidden;
	}
	
	.registration-page::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
		opacity: 0.3;
	}
	
	.registration-container {
		position: relative;
		z-index: 1;
	}
	
	.registration-card {
		background: #ffffff;
		border-radius: 20px;
		box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
		overflow: hidden;
		display: flex;
		min-height: 600px;
	}
	
	.registration-left {
		flex: 1;
		padding: 50px;
		display: flex;
		flex-direction: column;
		justify-content: center;
	}
	
	.registration-right {
		flex: 1;
		background: linear-gradient(135deg, var(--role-primary) 0%, var(--role-secondary) 100%);
		padding: 50px;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		color: white;
		position: relative;
		overflow: hidden;
	}
	
	.registration-right::before {
		content: '';
		position: absolute;
		top: -50%;
		right: -50%;
		width: 200%;
		height: 200%;
		background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
		animation: pulse 15s ease-in-out infinite;
	}
	
	@keyframes pulse {
		0%, 100% { transform: scale(1); opacity: 0.5; }
		50% { transform: scale(1.1); opacity: 0.8; }
	}
	
	.role-badge {
		display: inline-block;
		padding: 8px 20px;
		border-radius: 50px;
		background: var(--role-primary);
		color: white;
		font-size: 14px;
		font-weight: 600;
		margin-bottom: 20px;
		text-transform: uppercase;
		letter-spacing: 1px;
	}
	
	.registration-title {
		font-size: 36px;
		font-weight: 700;
		margin-bottom: 10px;
		color: #1a1a1a;
		line-height: 1.2;
	}
	
	.registration-subtitle {
		font-size: 16px;
		color: #666;
		margin-bottom: 40px;
		line-height: 1.6;
	}
	
	.form-group-modern {
		margin-bottom: 25px;
		position: relative;
	}
	
	.form-label-modern {
		display: block;
		font-weight: 600;
		color: #333;
		margin-bottom: 8px;
		font-size: 14px;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}
	
	.form-label-modern i {
		margin-right: 8px;
		color: var(--role-primary);
	}
	
	.form-control-modern {
		width: 100%;
		padding: 14px 18px;
		border: 2px solid #e0e0e0;
		border-radius: 10px;
		font-size: 16px;
		transition: all 0.3s ease;
		background: #fafafa;
	}
	
	.form-control-modern:focus {
		outline: none;
		border-color: var(--role-primary);
		background: #fff;
		box-shadow: 0 0 0 4px rgba(var(--role-primary-rgb), 0.1);
	}
	
	.form-control-modern.is-invalid {
		border-color: #dc3545;
		background: #fff5f5;
	}
	
	.input-icon {
		position: absolute;
		right: 15px;
		top: 50%;
		transform: translateY(-50%);
		color: #999;
		pointer-events: none;
	}
	
	.password-strength {
		margin-top: 8px;
		height: 4px;
		background: #e0e0e0;
		border-radius: 2px;
		overflow: hidden;
	}
	
	.password-strength-bar {
		height: 100%;
		width: 0%;
		transition: all 0.3s ease;
		background: #dc3545;
	}
	
	.password-strength-bar.weak { width: 33%; background: #dc3545; }
	.password-strength-bar.medium { width: 66%; background: #ffc107; }
	.password-strength-bar.strong { width: 100%; background: #28a745; }
	
	.btn-register {
		width: 100%;
		padding: 16px;
		background: var(--role-primary);
		color: white;
		border: none;
		border-radius: 10px;
		font-size: 16px;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.3s ease;
		text-transform: uppercase;
		letter-spacing: 1px;
		position: relative;
		overflow: hidden;
	}
	
	.btn-register::before {
		content: '';
		position: absolute;
		top: 50%;
		left: 50%;
		width: 0;
		height: 0;
		border-radius: 50%;
		background: rgba(255, 255, 255, 0.3);
		transform: translate(-50%, -50%);
		transition: width 0.6s, height 0.6s;
	}
	
	.btn-register:hover::before {
		width: 300px;
		height: 300px;
	}
	
	.btn-register:hover {
		transform: translateY(-2px);
		box-shadow: 0 10px 25px rgba(var(--role-primary-rgb), 0.3);
	}
	
	.btn-register:active {
		transform: translateY(0);
	}
	
	.role-icon {
		font-size: 80px;
		margin-bottom: 20px;
		opacity: 0.9;
	}
	
	.role-benefits {
		list-style: none;
		padding: 0;
		margin: 30px 0;
	}
	
	.role-benefits li {
		padding: 12px 0;
		font-size: 16px;
		display: flex;
		align-items: center;
	}
	
	.role-benefits li i {
		margin-right: 12px;
		font-size: 20px;
	}
	
	.already-have-account {
		text-align: center;
		margin-top: 30px;
		color: #666;
	}
	
	.already-have-account a {
		color: var(--role-primary);
		text-decoration: none;
		font-weight: 600;
	}
	
	.already-have-account a:hover {
		text-decoration: underline;
	}
	
	.error-message {
		color: #dc3545;
		font-size: 13px;
		margin-top: 5px;
		display: flex;
		align-items: center;
	}
	
	.error-message i {
		margin-right: 5px;
	}
	
	.other-roles {
		margin-top: 50px;
		padding: 30px;
		background: #f8f9fa;
		border-radius: 15px;
		text-align: center;
	}
	
	.other-roles-title {
		font-size: 18px;
		font-weight: 600;
		margin-bottom: 20px;
		color: #333;
	}
	
	.role-buttons {
		display: flex;
		gap: 15px;
		justify-content: center;
		flex-wrap: wrap;
	}
	
	.role-button {
		padding: 12px 24px;
		border: 2px solid #e0e0e0;
		border-radius: 8px;
		background: white;
		color: #666;
		text-decoration: none;
		font-weight: 500;
		transition: all 0.3s ease;
		font-size: 14px;
	}
	
	.role-button:hover {
		border-color: var(--role-primary);
		color: var(--role-primary);
		transform: translateY(-2px);
		box-shadow: 0 4px 12px rgba(0,0,0,0.1);
	}
	
	@media (max-width: 768px) {
		.registration-card {
			flex-direction: column;
		}
		
		.registration-right {
			display: none;
		}
		
		.registration-left {
			padding: 30px 20px;
		}
		
		.registration-title {
			font-size: 28px;
		}
	}
</style>

@php
	$roleColors = [
		'carer' => ['primary' => '#4CAF50', 'secondary' => '#45a049', 'rgb' => '76, 175, 80'],
		'housekeeper' => ['primary' => '#2196F3', 'secondary' => '#1976D2', 'rgb' => '33, 150, 243'],
		'childminder' => ['primary' => '#FF9800', 'secondary' => '#F57C00', 'rgb' => '255, 152, 0'],
		'client' => ['primary' => '#9C27B0', 'secondary' => '#7B1FA2', 'rgb' => '156, 39, 176'],
	];
	
	$roleSlug = strtolower($role->slug ?? 'client');
	$colors = $roleColors[$roleSlug] ?? $roleColors['client'];
	
	$roleBenefits = [
		'carer' => [
			['icon' => 'fa-heart', 'text' => 'Make a real difference in people\'s lives'],
			['icon' => 'fa-pound-sign', 'text' => 'Competitive rates and flexible hours'],
			['icon' => 'fa-shield-alt', 'text' => 'Verified and secure platform'],
			['icon' => 'fa-users', 'text' => 'Connect with clients who need your help'],
		],
		'housekeeper' => [
			['icon' => 'fa-home', 'text' => 'Work in comfortable home environments'],
			['icon' => 'fa-calendar-alt', 'text' => 'Flexible scheduling options'],
			['icon' => 'fa-star', 'text' => 'Build your reputation and reviews'],
			['icon' => 'fa-handshake', 'text' => 'Direct client relationships'],
		],
		'childminder' => [
			['icon' => 'fa-child', 'text' => 'Nurture and care for children'],
			['icon' => 'fa-graduation-cap', 'text' => 'Professional development opportunities'],
			['icon' => 'fa-clock', 'text' => 'Flexible working arrangements'],
			['icon' => 'fa-heart', 'text' => 'Rewarding career path'],
		],
		'client' => [
			['icon' => 'fa-search', 'text' => 'Find verified, qualified professionals'],
			['icon' => 'fa-shield-alt', 'text' => 'Safe and secure platform'],
			['icon' => 'fa-star', 'text' => 'Read reviews and ratings'],
			['icon' => 'fa-comments', 'text' => 'Direct communication with providers'],
		],
	];
	
	$benefits = $roleBenefits[$roleSlug] ?? $roleBenefits['client'];
	$roleIcons = [
		'carer' => 'fa-user-md',
		'housekeeper' => 'fa-broom',
		'childminder' => 'fa-baby',
		'client' => 'fa-user-friends',
	];
	$roleIcon = $roleIcons[$roleSlug] ?? 'fa-user';
@endphp

<div class="registration-page">
	<div class="container registration-container">
		<div class="registration-card">
			<div class="registration-left">
				<span class="role-badge" style="--role-primary: {{ $colors['primary'] }};">
					<i class="fa {{ $roleIcon }}"></i> {{ $role->title }}
				</span>
				
				<h1 class="registration-title">
					Join as a {{ $role->title }}
				</h1>
				<p class="registration-subtitle">
					Create your account and start your journey with JimaCare today. It only takes a few minutes!
				</p>
				
				<form method="POST" action="{{ route('register') }}" id="registrationForm">
					@csrf
					<input type="hidden" name="role" value="{{ $role->id }}">
					
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group-modern">
								<label for="firstname" class="form-label-modern">
									<i class="fa fa-user"></i> First Name
								</label>
								<input type="text" 
									   name="firstname" 
									   id="firstname" 
									   value="{{ old('firstname') }}"
									   class="form-control-modern @error('firstname') is-invalid @enderror"
									   placeholder="Enter your first name" 
									   required 
									   autofocus>
								@error('firstname')
									<div class="error-message">
										<i class="fa fa-exclamation-circle"></i> {{ $message }}
									</div>
								@enderror
							</div>
						</div>
						
						<div class="col-12 col-md-6">
							<div class="form-group-modern">
								<label for="lastname" class="form-label-modern">
									<i class="fa fa-user"></i> Last Name
								</label>
								<input type="text" 
									   name="lastname" 
									   id="lastname" 
									   value="{{ old('lastname') }}"
									   class="form-control-modern @error('lastname') is-invalid @enderror"
									   placeholder="Enter your last name" 
									   required>
								@error('lastname')
									<div class="error-message">
										<i class="fa fa-exclamation-circle"></i> {{ $message }}
									</div>
								@enderror
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group-modern">
								<label for="email" class="form-label-modern">
									<i class="fa fa-envelope"></i> Email Address
								</label>
								<input type="email" 
									   name="email" 
									   id="email" 
									   value="{{ old('email') }}"
									   class="form-control-modern @error('email') is-invalid @enderror"
									   placeholder="your.email@example.com" 
									   required>
								@error('email')
									<div class="error-message">
										<i class="fa fa-exclamation-circle"></i> {{ $message }}
									</div>
								@enderror
							</div>
						</div>
						
						<div class="col-12 col-md-6">
							<div class="form-group-modern">
								<label for="phone" class="form-label-modern">
									<i class="fa fa-phone"></i> Phone Number
								</label>
							<input type="tel" 
								   name="phone" 
								   id="phone" 
								   value="{{ old('phone') }}"
								   class="form-control-modern @error('phone') is-invalid @enderror"
								   placeholder="+44 7700 900000 or 07700 900000" 
								   required>
							<small style="color: #666; font-size: 12px; margin-top: 5px; display: block;">
								UK phone numbers only. Format: +44 7700 900000 or 07700 900000
							</small>
								@error('phone')
									<div class="error-message">
										<i class="fa fa-exclamation-circle"></i> {{ $message }}
									</div>
								@enderror
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group-modern">
								<label for="password" class="form-label-modern">
									<i class="fa fa-lock"></i> Password
								</label>
								<input type="password" 
									   name="password" 
									   id="password" 
									   class="form-control-modern @error('password') is-invalid @enderror"
									   placeholder="Create a strong password" 
									   required>
								<div class="password-strength">
									<div class="password-strength-bar" id="passwordStrength"></div>
								</div>
								@error('password')
									<div class="error-message">
										<i class="fa fa-exclamation-circle"></i> {{ $message }}
									</div>
								@enderror
							</div>
						</div>
						
						<div class="col-12 col-md-6">
							<div class="form-group-modern">
								<label for="password_confirmation" class="form-label-modern">
									<i class="fa fa-lock"></i> Confirm Password
								</label>
								<input type="password" 
									   name="password_confirmation" 
									   id="password_confirmation" 
									   class="form-control-modern @error('password_confirmation') is-invalid @enderror"
									   placeholder="Re-enter your password" 
									   required>
								@error('password_confirmation')
									<div class="error-message">
										<i class="fa fa-exclamation-circle"></i> {{ $message }}
									</div>
								@enderror
							</div>
						</div>
					</div>
					
					<button type="submit" class="btn-register" style="--role-primary: {{ $colors['primary'] }}; --role-primary-rgb: {{ $colors['rgb'] }};">
						Create Account <i class="fa fa-arrow-right ml-2"></i>
					</button>
					
					<div class="already-have-account">
						Already have an account? <a href="{{ route('login') }}">Sign in here</a>
					</div>
				</form>
			</div>
			
			<div class="registration-right" style="--role-primary: {{ $colors['primary'] }}; --role-secondary: {{ $colors['secondary'] }};">
				<div style="position: relative; z-index: 2; text-align: center;">
					<div class="role-icon">
						<i class="fa {{ $roleIcon }}"></i>
					</div>
					<h2 style="font-size: 32px; font-weight: 700; margin-bottom: 20px;">
						Why Join as a {{ $role->title }}?
					</h2>
					<ul class="role-benefits">
						@foreach($benefits as $benefit)
							<li>
								<i class="fa {{ $benefit['icon'] }}"></i>
								<span>{{ $benefit['text'] }}</span>
							</li>
						@endforeach
					</ul>
				</div>
			</div>
		</div>
		
		<div class="other-roles">
			<div class="other-roles-title">
				<i class="fa fa-users"></i> Want to join in a different role?
			</div>
			<div class="role-buttons">
				@foreach(['carer', 'housekeeper', 'childminder', 'client'] as $otherRole)
					@if($otherRole !== $roleSlug)
						@php
							$otherRoleModel = \App\Models\Role::where('slug', $otherRole)->first();
							if (!$otherRoleModel) continue;
							$otherRoleColors = $roleColors[$otherRole] ?? $roleColors['client'];
						@endphp
						<a href="{{ route('register.type', ['type' => $otherRole]) }}" 
						   class="role-button" 
						   style="--role-primary: {{ $otherRoleColors['primary'] }};">
							<i class="fa {{ $roleIcons[$otherRole] ?? 'fa-user' }}"></i> 
							{{ $otherRoleModel->title }}
						</a>
					@endif
				@endforeach
			</div>
		</div>
	</div>
</div>

<script>
	// Password strength indicator
	const passwordInput = document.getElementById('password');
	const strengthBar = document.getElementById('passwordStrength');
	
	if (passwordInput && strengthBar) {
		passwordInput.addEventListener('input', function() {
			const password = this.value;
			let strength = 0;
			
			if (password.length >= 8) strength++;
			if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
			if (password.match(/\d/)) strength++;
			if (password.match(/[^a-zA-Z\d]/)) strength++;
			
			strengthBar.className = 'password-strength-bar';
			if (strength <= 1) {
				strengthBar.classList.add('weak');
			} else if (strength <= 2) {
				strengthBar.classList.add('medium');
			} else {
				strengthBar.classList.add('strong');
			}
		});
	}
	
	// Form validation feedback
	const form = document.getElementById('registrationForm');
	if (form) {
		form.addEventListener('submit', function(e) {
			const inputs = form.querySelectorAll('input[required]');
			let isValid = true;
			
			inputs.forEach(input => {
				if (!input.value.trim()) {
					isValid = false;
					input.classList.add('is-invalid');
				}
			});
			
			if (!isValid) {
				e.preventDefault();
			}
		});
	}
</script>
@endsection
