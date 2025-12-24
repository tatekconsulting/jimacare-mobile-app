@extends('app.template.layout-profile')

@section('content')
<div class="container py-4">
	<div class="card shadow-sm border-0">
		<div class="card-header bg-primary text-white">
			<h4 class="mb-0"><i class="fa fa-video-camera"></i> Upload Professional Video</h4>
			<small>Showcase your professionalism with a short introduction video</small>
		</div>
		<div class="card-body">
			<form method="POST" action="{{ route('video') }}" enctype="multipart/form-data" id="videoUploadForm" class="novalidate">
				@csrf
				
				<!-- Drag & Drop Zone (TikTok/Facebook-like) -->
				<div class="upload-zone" id="videoUploadZone">
					<div class="upload-zone-content text-center">
						@if (Storage::disk('s3')->exists($profile->video))
							<div class="video-preview-container mb-3">
								<video id="videoPreview" class="video-preview" controls>
									<source src="{{ Storage::disk('s3')->temporaryUrl($profile->video, Carbon\Carbon::now()->addMinutes(60)) }}" type="video/mp4">
									Your browser does not support the video tag.
								</video>
								<button type="button" class="btn btn-danger btn-sm mt-2" id="removeVideoBtn">
									<i class="fa fa-trash"></i> Remove Current Video
								</button>
							</div>
						@else
							<div class="upload-placeholder">
								<i class="fa fa-cloud-upload fa-4x text-primary mb-3"></i>
								<h5 class="mb-2">Drag & Drop Your Video Here</h5>
								<p class="text-muted mb-3">or click to browse</p>
								<button type="button" class="btn btn-primary btn-lg" id="browseVideoBtn">
									<i class="fa fa-folder-open"></i> Choose Video File
								</button>
								<div class="upload-info mt-3">
									<small class="text-muted">
										<i class="fa fa-info-circle"></i> 
										Max size: 8MB | Formats: MP4, AVI, MOV, WebM
									</small>
								</div>
							</div>
						@endif
					</div>
					
					<!-- Hidden file input -->
					<input type="file" name="video" id="videoInput" class="d-none" accept="video/*"/>
					
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
						<i class="fa fa-check-circle"></i> <strong>Success!</strong> Video uploaded successfully.
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

.video-preview-container {
	position: relative;
	max-width: 600px;
	margin: 0 auto;
}

.video-preview {
	width: 100%;
	max-height: 400px;
	border-radius: 8px;
	box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
	const uploadZone = document.getElementById('videoUploadZone');
	const videoInput = document.getElementById('videoInput');
	const browseBtn = document.getElementById('browseVideoBtn');
	const removeBtn = document.getElementById('removeVideoBtn');
	const uploadForm = document.getElementById('videoUploadForm');
	const progressDiv = document.getElementById('uploadProgress');
	const progressBar = document.getElementById('progressBar');
	const progressText = document.getElementById('progressText');
	const successMessage = document.getElementById('successMessage');
	const errorMessage = document.getElementById('errorMessage');
	const errorText = document.getElementById('errorText');
	const videoPreview = document.getElementById('videoPreview');

	// Click to browse
	if (browseBtn) {
		browseBtn.addEventListener('click', () => videoInput.click());
	}
	uploadZone.addEventListener('click', (e) => {
		if (e.target !== browseBtn && e.target !== removeBtn) {
			videoInput.click();
		}
	});

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
	videoInput.addEventListener('change', (e) => {
		if (e.target.files.length > 0) {
			handleFileSelect(e.target.files[0]);
		}
	});

	// Handle file selection
	function handleFileSelect(file) {
		// Validate file type
		const allowedTypes = ['video/mp4', 'video/avi', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv', 'video/x-flv', 'video/webm'];
		if (!allowedTypes.includes(file.type)) {
			showError('Invalid file type. Please upload MP4, AVI, MOV, WMV, FLV, or WebM format.');
			return;
		}

		// Validate file size (8MB = 8 * 1024 * 1024 bytes)
		const maxSize = 8 * 1024 * 1024;
		if (file.size > maxSize) {
			showError('File size exceeds 8MB limit. Please choose a smaller file.');
			return;
		}

		// Show preview
		const reader = new FileReader();
		reader.onload = (e) => {
			if (videoPreview) {
				videoPreview.src = e.target.result;
				videoPreview.style.display = 'block';
			}
		};
		reader.readAsDataURL(file);

		// Upload file
		uploadFile(file);
	}

	// Upload file with progress
	function uploadFile(file) {
		const formData = new FormData();
		formData.append('video', file);
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
						if (response.path && videoPreview) {
							videoPreview.src = response.path;
						}
						// Reload page after 2 seconds to show new video
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

		xhr.open('POST', '{{ route("video") }}');
		xhr.send(formData);
	}

	// Remove video
	if (removeBtn) {
		removeBtn.addEventListener('click', () => {
			if (confirm('Are you sure you want to remove this video?')) {
				const formData = new FormData();
				formData.append('action', 'remove');
				formData.append('_token', '{{ csrf_token() }}');

				fetch('{{ route("video") }}', {
					method: 'POST',
					body: formData
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						window.location.reload();
					} else {
						showError('Failed to remove video. Please try again.');
					}
				})
				.catch(() => {
					showError('Network error. Please try again.');
				});
			}
		});
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

