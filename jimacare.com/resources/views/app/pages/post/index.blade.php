@extends('app.template.layout')

@section('content')
<style>
    .blog-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 60px 0;
        color: white;
        margin-bottom: 50px;
    }
    
    .blog-hero h1 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 15px;
    }
    
    .blog-hero p {
        font-size: 1.2rem;
        opacity: 0.9;
    }
    
    .blog-search-filter {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 40px;
    }
    
    .blog-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .blog-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    }
    
    .blog-card-image {
        width: 100%;
        height: 250px;
        object-fit: cover;
        background: #f0f0f0;
    }
    
    .blog-card-body {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    
    .blog-card-category {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .blog-card-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 12px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .blog-card-excerpt {
        color: #718096;
        font-size: 0.95rem;
        line-height: 1.7;
        margin-bottom: 20px;
        flex-grow: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .blog-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #e9ecef;
    }
    
    .blog-card-date {
        color: #a0aec0;
        font-size: 0.85rem;
    }
    
    .blog-card-link {
        color: #667eea;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }
    
    .blog-card-link:hover {
        color: #764ba2;
        gap: 12px;
    }
    
    .filter-badge {
        display: inline-block;
        padding: 8px 20px;
        margin: 5px;
        border: 2px solid #667eea;
        border-radius: 25px;
        background: white;
        color: #667eea;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .filter-badge:hover,
    .filter-badge.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: transparent;
    }
    
    .search-input-group {
        position: relative;
    }
    
    .search-input-group input {
        border-radius: 25px;
        padding: 12px 50px 12px 20px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .search-input-group input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .search-input-group button {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        padding: 8px 20px;
        cursor: pointer;
    }
    
    .no-posts {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .no-posts-icon {
        font-size: 4rem;
        color: #cbd5e0;
        margin-bottom: 20px;
    }
    
    .pagination-wrapper {
        margin-top: 50px;
        display: flex;
        justify-content: center;
    }
</style>

<!-- Blog Hero Section -->
<div class="blog-hero">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1>JimaCare Blog</h1>
                <p>Insights, Tips, and Resources for Care Professionals</p>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="container">
    <div class="blog-search-filter">
        <form method="GET" action="{{ route('blog') }}" class="row">
            <div class="col-12 col-md-8 mb-3 mb-md-0">
                <div class="search-input-group">
                    <input type="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           class="form-control" 
                           placeholder="Search articles..."
                    />
                    <button type="submit" class="btn">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="text-center text-md-right">
                    <button type="submit" class="btn btn-primary" style="border-radius: 25px; padding: 12px 30px;">
                        <i class="fa fa-filter"></i> Apply Filters
                    </button>
                </div>
            </div>
            <div class="col-12 mt-3">
                <div class="filter-badges-container">
                    <label class="mb-2 d-block"><strong>Filter by Category:</strong></label>
                    @foreach($roles as $role)
                        <label class="filter-badge @if(in_array($role->id, request('type', []))) active @endif">
                            <input type="checkbox" 
                                   name="type[]" 
                                   value="{{ $role->id }}"
                                   class="d-none"
                                   @if(in_array($role->id, request('type', []))) checked @endif
                                   onchange="this.closest('form').submit()"
                            />
                            {{ $role->title }}
                        </label>
                    @endforeach
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Blog Posts Grid -->
<div class="container">
    @if($posts->count() > 0)
        <div class="row">
            @foreach($posts as $post)
                <div class="col-12 col-sm-6 col-lg-4 mb-4">
                    <article class="blog-card">
                        <a href="{{ route('post', ['post' => $post->id]) }}" style="text-decoration: none; color: inherit;">
                            <img src="{{ asset($post->image ?? 'img/default-blog.jpg') }}" 
                                 alt="{{ $post->title }}"
                                 class="blog-card-image"
                                 onerror="this.src='{{ asset('img/default-blog.jpg') }}'"
                            />
                            <div class="blog-card-body">
                                <span class="blog-card-category">{{ $post->role->title ?? 'General' }}</span>
                                <h3 class="blog-card-title">{{ $post->title }}</h3>
                                <p class="blog-card-excerpt">
                                    {!! strip_tags(substr($post->desc, 0, 150)) !!}...
                                </p>
                                <div class="blog-card-footer">
                                    <span class="blog-card-date">
                                        <i class="fa fa-calendar"></i> 
                                        {{ $post->created_at->format('M d, Y') }}
                                    </span>
                                    <span class="blog-card-link">
                                        Read More <i class="fa fa-arrow-right"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </article>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $posts->links('app.template.pagination') }}
        </div>
    @else
        <div class="no-posts">
            <div class="no-posts-icon">
                <i class="fa fa-file-text"></i>
            </div>
            <h3>No Articles Found</h3>
            <p class="text-muted">Try adjusting your search terms or filters</p>
        </div>
    @endif
</div>

@endsection
