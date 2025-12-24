@extends('app.template.layout-profile')

@section('content')
<div class="container py-4">
	<div class="card shadow-sm border-0">
		<div class="card-header bg-primary text-white">
			<h4 class="mb-0"><i class="fa fa-camera"></i> Upload Profile Photo</h4>
			<small>Upload a professional profile picture</small>
		</div>
		<div class="card-body">
			@if($profile->profile_locked)
				<div class="alert alert-warning">
					<i class="fa fa-lock"></i> <strong>Profile Photo Locked</strong>
					<p class="mb-0 mt-2">Your profile photo has been locked and cannot be changed. Please contact admin if you need to update it.</p>
					@if($profile->profile_verified_at)
						<p class="mb-0 mt-2">
							<i class="fa fa-check-circle text-success"></i> 
							<strong>Face Verified:</strong> {{ $profile->profile_verified_at->format('d M Y H:i') }}
						</p>
					@endif
				</div>
			@endif
			
			<form method="POST" action="{{ route('photo') }}" enctype="multipart/form-data" id="photoUploadForm" class="novalidate">
				@csrf
				
				<!-- Drag & Drop Zone (User-friendly) -->
				<div class="upload-zone {{ $profile->profile_locked ? 'locked' : '' }}" id="photoUploadZone" 
					 @if($profile->profile_locked) style="pointer-events: none; opacity: 0.6;" @endif>
					<div class="upload-zone-content text-center">
						@if($profile->profile)
							<div class="photo-preview-container mb-3">
								<div class="photo-preview-wrapper">
									<img id="photoPreview" class="photo-preview" src="{{ asset($profile->profile) }}" alt="Profile Photo">
									@if(!$profile->profile_locked)
										<div class="photo-overlay">
											<button type="button" class="btn btn-light btn-sm" id="changePhotoBtn">
												<i class="fa fa-camera"></i> Change Photo
											</button>
										</div>
									@else
										<div class="photo-overlay locked-overlay">
											<i class="fa fa-lock fa-3x text-white"></i>
											<p class="text-white mt-2">Locked</p>
										</div>
									@endif
								</div>
							</div>
						@else
							<div class="upload-placeholder">
								<i class="fa fa-cloud-upload fa-4x text-primary mb-3"></i>
								<h5 class="mb-2">Drag & Drop Your Photo Here</h5>
								<p class="text-muted mb-3">or click to browse</p>
								<button type="button" class="btn btn-primary btn-lg" id="browsePhotoBtn">
									<i class="fa fa-folder-open"></i> Choose Photo
								</button>
								<div class="upload-info mt-3">
									<small class="text-muted">
										<i class="fa fa-info-circle"></i> 
										Max size: 5MB | Formats: JPG, PNG, GIF, WebP | Min: 200x200px
									</small>
								</div>
							</div>
						@endif
					</div>
					
					<!-- Hidden file input -->
					<input type="file" name="profile" id="photoInput" class="d-none" accept="image/*" 
						   @if($profile->profile_locked) disabled @endif/>
					
					<!-- Progress bar -->
					<div class="upload-progress d-none" id="uploadProgress">
						<div class="progress mb-2" style="height: 25px;">
							<div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
								 role="progressbar" 
								 style="width: 0%" 
								 id="progressBar">
								0%
							</div>
						</div>
						<div class="text-center">
							<small class="text-muted" id="progressText">Uploading...</small>
						</div>
					</div>
					
					<!-- Success message -->
					<div class="alert alert-success d-none" id="successMessage">
						<i class="fa fa-check-circle"></i> <strong>Success!</strong> Photo uploaded successfully.
					</div>
					
					<!-- Error message -->
					<div class="alert alert-danger d-none" id="errorMessage">
						<i class="fa fa-exclamation-circle"></i> <strong>Error!</strong> <span id="errorText"></span>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<style>
.upload-zone {
	border: 3px dashed #dee2e6;
	border-radius: 12px;
	padding: 40px 20px;
	background: #f8f9fa;
	transition: all 0.3s ease;
	cursor: pointer;
	min-height: 300px;
	display: flex;
	align-items: center;
	justify-content: center;
}

.upload-zone:hover {
	border-color: #007bff;
	background: #e7f3ff;
}

.upload-zone.dragover {
	border-color: #007bff;
	background: #cfe2ff;
	transform: scale(1.02);
}

.upload-placeholder {
	padding: 20px;
}

.photo-preview-container {
	position: relative;
	max-width: 400px;
	margin: 0 auto;
}

.photo-preview-wrapper {
	position: relative;
	border-radius: 50%;
	overflow: hidden;
	width: 300px;
	height: 300px;
	margin: 0 auto;
	border: 5px solid #fff;
	box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.photo-preview {
	width: 100%;
	height: 100%;
	object-fit: cover;
}

.photo-overlay {
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: rgba(0,0,0,0.5);
	display: flex;
	align-items: center;
	justify-content: center;
	opacity: 0;
	transition: opacity 0.3s ease;
}

.photo-preview-wrapper:hover .photo-overlay {
	opacity: 1;
}

.upload-progress {
	margin-top: 20px;
}

.progress {
	border-radius: 10px;
	overflow: hidden;
}

.progress-bar {
	font-weight: bold;
	display: flex;
	align-items: center;
	justify-content: center;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const uploadZone = document.getElementById('photoUploadZone');
	const photoInput = document.getElementById('photoInput');
	const browseBtn = document.getElementById('browsePhotoBtn');
	const changeBtn = document.getElementById('changePhotoBtn');
	const uploadForm = document.getElementById('photoUploadForm');
	const progressDiv = document.getElementById('uploadProgress');
	const progressBar = document.getElementById('progressBar');
	const progressText = document.getElementById('progressText');
	const successMessage = document.getElementById('successMessage');
	const errorMessage = document.getElementById('errorMessage');
	const errorText = document.getElementById('errorText');
	const photoPreview = document.getElementById('photoPreview');

	// Check if profile is locked
	const isLocked = {{ $profile->profile_locked ? 'true' : 'false' }};
	
	// Click to browse (only if not locked)
	if (browseBtn && !isLocked) {
		browseBtn.addEventListener('click', () => photoInput.click());
	}
	if (changeBtn && !isLocked) {
		changeBtn.addEventListener('click', () => photoInput.click());
	}
	if (!isLocked) {
		uploadZone.addEventListener('click', (e) => {
			if (e.target !== browseBtn && e.target !== changeBtn) {
				photoInput.click();
			}
		});
	}

	// Drag and drop
	uploadZone.addEventListener('dragover', (e) => {
		e.preventDefault();
		uploadZone.classList.add('dragover');
	});

	uploadZone.addEventListener('dragleave', () => {
		uploadZone.classList.remove('dragover');
	});

	uploadZone.addEventListener('drop', (e) => {
		e.preventDefault();
		uploadZone.classList.remove('dragover');
		
		const files = e.dataTransfer.files;
		if (files.length > 0) {
			handleFileSelect(files[0]);
		}
	});

	// File input change
	photoInput.addEventListener('change', (e) => {
		if (e.target.files.length > 0) {
			handleFileSelect(e.target.files[0]);
		}
	});

	// Handle file selection
	function handleFileSelect(file) {
		// Validate file type
		if (!file.type.startsWith('image/')) {
			showError('Invalid file type. Please upload an image file (JPG, PNG, GIF, WebP).');
			return;
		}

		// Validate file size (5MB = 5 * 1024 * 1024 bytes)
		const maxSize = 5 * 1024 * 1024;
		if (file.size > maxSize) {
			showError('File size exceeds 5MB limit. Please choose a smaller file.');
			return;
		}

		// Validate image dimensions
		const img = new Image();
		img.onload = () => {
			if (img.width < 200 || img.height < 200) {
				showError('Image dimensions must be at least 200x200 pixels.');
				return;
			}
			if (img.width > 5000 || img.height > 5000) {
				showError('Image dimensions must not exceed 5000x5000 pixels.');
				return;
			}
			// Show preview
			const reader = new FileReader();
			reader.onload = (e) => {
				if (photoPreview) {
					photoPreview.src = e.target.result;
				}
			};
			reader.readAsDataURL(file);
			// Upload file
			uploadFile(file);
		};
		img.onerror = () => {
			showError('Invalid image file. Please choose a valid image.');
		};
		img.src = URL.createObjectURL(file);
	}

	// Upload file with progress
	function uploadFile(file) {
		// Check if locked
		if (isLocked) {
			showError('Profile photo is locked and cannot be changed. Please contact admin if you need to update it.');
			return;
		}
		
		const formData = new FormData();
		formData.append('profile', file);
		formData.append('_token', '{{ csrf_token() }}');

		// Show progress
		progressDiv.classList.remove('d-none');
		successMessage.classList.add('d-none');
		errorMessage.classList.add('d-none');
		progressBar.style.width = '0%';
		progressBar.textContent = '0%';
		progressText.textContent = 'Uploading...';

		const xhr = new XMLHttpRequest();

		// Upload progress
		xhr.upload.addEventListener('progress', (e) => {
			if (e.lengthComputable) {
				const percentComplete = (e.loaded / e.total) * 100;
				progressBar.style.width = percentComplete + '%';
				progressBar.textContent = Math.round(percentComplete) + '%';
				progressText.textContent = `Uploading... ${Math.round(percentComplete)}%`;
			}
		});

		// Upload complete
		xhr.addEventListener('load', () => {
			if (xhr.status === 200) {
				const response = JSON.parse(xhr.responseText);
				if (response.success) {
					progressText.textContent = 'Processing...';
					setTimeout(() => {
						progressDiv.classList.add('d-none');
						successMessage.classList.remove('d-none');
						if (response.path && photoPreview) {
							photoPreview.src = response.path;
						}
						// Reload page after 2 seconds to show new photo
						setTimeout(() => window.location.reload(), 2000);
					}, 1000);
				} else {
					showError(response.error || 'Upload failed. Please try again.');
				}
			} else {
				const response = JSON.parse(xhr.responseText);
				showError(response.error || 'Upload failed. Please try again.');
			}
		});

		// Upload error
		xhr.addEventListener('error', () => {
			showError('Network error. Please check your connection and try again.');
		});

		xhr.open('POST', '{{ route("photo") }}');
		xhr.send(formData);
	}

	// Show error
	function showError(message) {
		errorText.textContent = message;
		errorMessage.classList.remove('d-none');
		progressDiv.classList.add('d-none');
	}
});
</script>
@endsection

