@extends('app.template.layout')

@section('content')
<style>
    .blog-single-hero {
        position: relative;
        height: 400px;
        overflow: hidden;
        margin-bottom: 50px;
    }
    
    .blog-single-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
        z-index: 1;
    }
    
    .blog-single-hero img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .blog-single-hero-content {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 40px;
        z-index: 2;
        color: white;
    }
    
    .blog-single-category {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        color: white;
        padding: 8px 20px;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .blog-single-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 15px;
        line-height: 1.3;
    }
    
    .blog-single-meta {
        display: flex;
        align-items: center;
        gap: 20px;
        font-size: 0.95rem;
        opacity: 0.9;
    }
    
    .blog-single-content {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .blog-single-body {
        background: white;
        border-radius: 16px;
        padding: 50px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 40px;
        line-height: 1.8;
        font-size: 1.1rem;
        color: #4a5568;
    }
    
    .blog-single-body h2,
    .blog-single-body h3,
    .blog-single-body h4 {
        color: #2d3748;
        margin-top: 30px;
        margin-bottom: 15px;
        font-weight: 700;
    }
    
    .blog-single-body p {
        margin-bottom: 20px;
    }
    
    .blog-single-body img {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        margin: 30px 0;
    }
    
    .blog-single-actions {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 50px;
    }
    
    .btn-back-blog {
        background: white;
        border: 2px solid #667eea;
        color: #667eea;
        padding: 12px 30px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-back-blog:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: transparent;
        text-decoration: none;
    }
    
    .blog-share-buttons {
        display: flex;
        gap: 10px;
    }
    
    .share-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f7fafc;
        color: #667eea;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .share-btn:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-decoration: none;
        transform: translateY(-3px);
    }
    
    @media (max-width: 768px) {
        .blog-single-title {
            font-size: 1.8rem;
        }
        
        .blog-single-body {
            padding: 30px 20px;
        }
        
        .blog-single-actions {
            flex-direction: column;
            gap: 20px;
        }
    }
</style>

<!-- Blog Single Hero -->
<div class="blog-single-hero">
    <img src="{{ asset($post->banner ?? $post->image ?? 'img/default-blog.jpg') }}" 
         alt="{{ $post->title }}"
         onerror="this.src='{{ asset('img/default-blog.jpg') }}'"
    />
    <div class="blog-single-hero-content">
        <div class="container">
            <span class="blog-single-category">{{ $post->role->title ?? 'General' }}</span>
            <h1 class="blog-single-title">{{ $post->title }}</h1>
            <div class="blog-single-meta">
                <span><i class="fa fa-calendar"></i> {{ $post->created_at->format('F d, Y') }}</span>
                <span><i class="fa fa-user"></i> JimaCare Team</span>
            </div>
        </div>
    </div>
</div>

<!-- Blog Content -->
<div class="blog-single-content">
    <article class="blog-single-body">
        {!! $post->desc !!}
    </article>
    
    <!-- Actions -->
    <div class="blog-single-actions">
        <a href="{{ route('blog') }}" class="btn-back-blog">
            <i class="fa fa-arrow-left"></i> Back to Blog
        </a>
        <div class="blog-share-buttons">
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
               target="_blank"
               class="share-btn"
               title="Share on Facebook">
                <i class="fa fa-facebook"></i>
            </a>
            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" 
               target="_blank"
               class="share-btn"
               title="Share on Twitter">
                <i class="fa fa-twitter"></i>
            </a>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($post->title) }}" 
               target="_blank"
               class="share-btn"
               title="Share on LinkedIn">
                <i class="fa fa-linkedin"></i>
            </a>
            <a href="mailto:?subject={{ urlencode($post->title) }}&body={{ urlencode(url()->current()) }}" 
               class="share-btn"
               title="Share via Email">
                <i class="fa fa-envelope"></i>
            </a>
        </div>
    </div>
</div>

@endsection
