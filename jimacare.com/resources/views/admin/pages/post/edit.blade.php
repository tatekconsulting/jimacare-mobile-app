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
        padding: 0;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f7fafc;
        overflow: hidden;
    }
    
    .image-upload-area:hover {
        border-color: #667eea;
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
    
    .image-upload-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .image-upload-area:hover .image-upload-overlay {
        opacity: 1;
    }
    
    .image-upload-overlay-text {
        color: white;
        font-weight: 600;
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
    
    .btn-update {
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
    
    .btn-update:hover {
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
            <i class="fa fa-edit text-primary"></i> Edit Blog Post
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

    <form action="{{ route('dashboard.post.update', ['post' => $post->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
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
                       value="{{ $post->title }}"
                       class="form-control @error('title') is-invalid @enderror"
                       placeholder="Enter a compelling title for your post"
                       required 
                       autofocus
                />
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
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
                        <option value="{{ $role->id }}" @if($post->role_id == $role->id) selected @endif>
                            {{ $role->title }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
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
                    <label class="form-label">Thumbnail Image</label>
                    <div class="image-upload-area" onclick="document.getElementById('image-input').click()">
                        <div class="image-upload-preview">
                            <img src="{{ asset($post->image) }}" alt="Current thumbnail" id="image-current">
                            <div class="image-upload-overlay">
                                <span class="image-upload-overlay-text">Click to change image</span>
                            </div>
                        </div>
                    </div>
                    <input type="file" 
                           name="image" 
                           id="image-input"
                           class="d-none"
                           accept="image/*"
                           onchange="previewImage(this, 'image-current')"
                    />
                    @error('image')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                    <div class="help-text">Leave empty to keep current image</div>
                </div>

                <div class="col-12 col-md-6 mb-3">
                    <label class="form-label">Banner Image</label>
                    <div class="image-upload-area" onclick="document.getElementById('banner-input').click()">
                        <div class="image-upload-preview">
                            <img src="{{ asset($post->banner) }}" alt="Current banner" id="banner-current">
                            <div class="image-upload-overlay">
                                <span class="image-upload-overlay-text">Click to change image</span>
                            </div>
                        </div>
                    </div>
                    <input type="file" 
                           name="banner" 
                           id="banner-input"
                           class="d-none"
                           accept="image/*"
                           onchange="previewImage(this, 'banner-current')"
                    />
                    @error('banner')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                    <div class="help-text">Leave empty to keep current image</div>
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
                >{{ $post->desc }}</textarea>
                @error('desc')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Actions -->
        <div class="form-section">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('dashboard.post.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn-update">
                    <i class="fa fa-save"></i> Update Post
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
            ]
        });
    });
    
    function previewImage(input, imgId) {
        const img = document.getElementById(imgId);
        const file = input.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endpush
