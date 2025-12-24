@extends('admin.template.layout')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<style>
    .post-form-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .form-section {
        background: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .form-section-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-section-title i {
        color: #667eea;
    }
    
    .image-upload-area {
        border: 2px dashed #cbd5e0;
        border-radius: 12px;
        padding: 40px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f7fafc;
    }
    
    .image-upload-area:hover {
        border-color: #667eea;
        background: #edf2f7;
    }
    
    .image-upload-area.has-image {
        border-color: #667eea;
        background: white;
        padding: 0;
        overflow: hidden;
    }
    
    .image-upload-area img {
        width: 100%;
        height: auto;
        max-height: 300px;
        object-fit: cover;
        display: block;
    }
    
    .image-upload-preview {
        position: relative;
    }
    
    .image-upload-remove {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .image-upload-remove:hover {
        background: #dc3545;
        transform: scale(1.1);
    }
    
    .form-label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 8px;
        display: block;
    }
    
    .form-control,
    .custom-select {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus,
    .custom-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }
    
    .note-editor {
        border-radius: 8px;
        border: 2px solid #e9ecef;
    }
    
    .btn-publish {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 15px 40px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    
    .btn-publish:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    
    .help-text {
        font-size: 0.85rem;
        color: #718096;
        margin-top: 5px;
    }
</style>

<div class="post-form-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">
            <i class="fa fa-plus-circle text-primary"></i> Create New Blog Post
        </h2>
        <a href="{{ route('dashboard.post.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left"></i> Back to Posts
        </a>
    </div>

    @if(session('notice'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('notice') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <form action="{{ route('dashboard.post.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Basic Information -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fa fa-info-circle"></i>
                <span>Basic Information</span>
            </div>
            
            <div class="form-group">
                <label for="title" class="form-label">Post Title *</label>
                <input type="text" 
                       name="title" 
                       id="title"
                       value="{{ old('title') }}"
                       class="form-control @error('title') is-invalid @enderror"
                       placeholder="Enter a compelling title for your post"
                       required 
                       autofocus
                />
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="help-text">A clear, descriptive title helps readers find your content</div>
            </div>

            <div class="form-group">
                <label for="type" class="form-label">Category *</label>
                <select name="type"
                        id="type"
                        class="custom-select @error('type') is-invalid @enderror"
                        required
                >
                    <option value="">Select a category</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" @if(old('type') == $role->id) selected @endif>
                            {{ $role->title }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="help-text">Choose the category that best fits your content</div>
            </div>
        </div>

        <!-- Images -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fa fa-image"></i>
                <span>Images</span>
            </div>
            
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label class="form-label">Thumbnail Image *</label>
                    <div class="image-upload-area" onclick="document.getElementById('image-input').click()">
                        <div class="image-upload-preview" id="image-preview">
                            <div class="text-center py-5">
                                <i class="fa fa-cloud-upload fa-3x text-muted mb-3"></i>
                                <p class="mb-0 text-muted">Click to upload thumbnail image</p>
                                <small class="text-muted">Recommended: 800x600px</small>
                            </div>
                        </div>
                    </div>
                    <input type="file" 
                           name="image" 
                           id="image-input"
                           class="d-none"
                           accept="image/*"
                           required
                           onchange="previewImage(this, 'image-preview')"
                    />
                    @error('image')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6 mb-3">
                    <label class="form-label">Banner Image *</label>
                    <div class="image-upload-area" onclick="document.getElementById('banner-input').click()">
                        <div class="image-upload-preview" id="banner-preview">
                            <div class="text-center py-5">
                                <i class="fa fa-cloud-upload fa-3x text-muted mb-3"></i>
                                <p class="mb-0 text-muted">Click to upload banner image</p>
                                <small class="text-muted">Recommended: 1200x400px</small>
                            </div>
                        </div>
                    </div>
                    <input type="file" 
                           name="banner" 
                           id="banner-input"
                           class="d-none"
                           accept="image/*"
                           required
                           onchange="previewImage(this, 'banner-preview')"
                    />
                    @error('banner')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fa fa-edit"></i>
                <span>Content</span>
            </div>
            
            <div class="form-group">
                <label for="desc" class="form-label">Post Content *</label>
                <textarea name="desc" 
                          id="desc"
                          class="form-control @error('desc') is-invalid @enderror"
                          rows="15"
                          placeholder="Write your blog post content here..."
                          required
                >{{ old('desc') }}</textarea>
                @error('desc')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="help-text">Use the editor toolbar to format your content. Include headings, lists, links, and images.</div>
            </div>
        </div>

        <!-- Actions -->
        <div class="form-section">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('dashboard.post.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn-publish">
                    <i class="fa fa-paper-plane"></i> Publish Post
                </button>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script>
    $(document).ready(function() {
        $('#desc').summernote({
            height: 400,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            placeholder: 'Start writing your blog post...',
            callbacks: {
                onImageUpload: function(files) {
                    uploadImage(files[0]);
                }
            }
        });
    });
    
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `
                    <div class="image-upload-preview">
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" class="image-upload-remove" onclick="removeImage('${input.id}', '${previewId}')">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                `;
                preview.closest('.image-upload-area').classList.add('has-image');
            };
            reader.readAsDataURL(file);
        }
    }
    
    function removeImage(inputId, previewId) {
        document.getElementById(inputId).value = '';
        const preview = document.getElementById(previewId);
        preview.innerHTML = `
            <div class="text-center py-5">
                <i class="fa fa-cloud-upload fa-3x text-muted mb-3"></i>
                <p class="mb-0 text-muted">Click to upload image</p>
            </div>
        `;
        preview.closest('.image-upload-area').classList.remove('has-image');
    }
    
    function uploadImage(file) {
        const data = new FormData();
        data.append('image', file);
        
        fetch('/admin/upload-image', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: data
        })
        .then(response => response.json())
        .then(data => {
            if (data.url) {
                $('#desc').summernote('insertImage', data.url);
            }
        })
        .catch(error => {
            console.error('Error uploading image:', error);
            alert('Failed to upload image. Please try again.');
        });
    }
</script>
@endpush
