@extends('app.template.layout')

@section('content')
<style>
    .sellers-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 3rem 2rem;
        color: white;
        margin-bottom: 3rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    
    .search-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
    }
    
    .form-control-modern {
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        padding: 0.875rem 1.25rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .form-control-modern:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }
    
    /* Multi-select experience dropdown styling */
    select[multiple].experience {
        background-image: none;
        padding-right: 0.75rem;
    }
    
    select[multiple].experience option {
        padding: 0.5rem;
        cursor: pointer;
    }
    
    select[multiple].experience option:hover {
        background-color: #667eea;
        color: white;
    }
    
    select[multiple].experience option:checked {
        background-color: #667eea;
        color: white;
    }
    
    .btn-search {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 0.875rem 2.5rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .btn-search:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .seller-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
    }
    
    .seller-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        border-color: #667eea;
    }
    
    .seller-avatar-modern {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #e2e8f0;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .seller-card:hover .seller-avatar-modern {
        border-color: #667eea;
        transform: scale(1.05);
    }
    
    .badge-verified {
        display: inline-flex;
        align-items: center;
        padding: 0.4rem 0.9rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        margin: 0.25rem 0.25rem 0.25rem 0;
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(72, 187, 120, 0.3);
    }
    
    .badge-verified i {
        margin-right: 0.4rem;
    }
    
    .badge-price {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(246, 173, 85, 0.3);
    }
    
    .info-snippet {
        color: #4a5568;
        line-height: 1.7;
        font-size: 0.95rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin: 1rem 0;
    }
    
    .btn-view-profile {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        width: 100%;
    }
    
    .btn-view-profile:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .rating-display {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        padding: 0.75rem;
        background: #f7fafc;
        border-radius: 12px;
    }
    
    .rating-display .rating {
        margin-right: 0.5rem;
    }
    
    .stats-item {
        display: flex;
        align-items: center;
        color: #718096;
        font-size: 0.9rem;
        margin: 0.5rem 0;
    }
    
    .stats-item i {
        width: 20px;
        margin-right: 0.5rem;
        color: #667eea;
    }
    
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    
    .empty-state-icon {
        font-size: 5rem;
        color: #cbd5e0;
        margin-bottom: 1.5rem;
    }
    
    .results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .results-count {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
    }
    
    .results-count span {
        color: #667eea;
    }
    
    @media (max-width: 768px) {
        .sellers-hero {
            padding: 2rem 1.5rem;
        }
        
        .seller-avatar-modern {
            width: 120px;
            height: 120px;
        }
        
        .results-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="pt-5 pb-5" style="background: #f7fafc; min-height: 100vh;">
    <div class="container">
        <!-- Hero Section -->
        <div class="sellers-hero text-center">
            <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem;">
                <i class="fa fa-users mr-3"></i>Find Your Perfect Care Provider
            </h1>
            <p style="font-size: 1.2rem; opacity: 0.95;">
                Search through verified carers, childminders, and housekeepers in your area
            </p>
        </div>

        <!-- Search Form -->
        <div class="search-card">
            <form method="get" class="location-autofill">
                <div class="row">
                    <div class="form-group col-12 col-md-3 mb-3 mb-md-0">
                        <label for="type" style="font-weight: 600; color: #2d3748; margin-bottom: 0.5rem;">
                            <i class="fa fa-filter mr-2"></i>I'm looking for
                        </label>
                        <select name="type" id="type" class="type type_filter form-control form-control-modern">
                            <option value="">All Types</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @if($role->id == request('type')) selected @endif>
                                    {{ ucfirst($role->title) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group col-12 col-md-2 mb-3 mb-md-0">
                        <label for="radius" style="font-weight: 600; color: #2d3748; margin-bottom: 0.5rem;">
                            <i class="fa fa-map-marker mr-2"></i>Within
                        </label>
                        @php
                            $radiuses = [1 => '1 Mile', 2 => '2 Miles', 3 => '3 Miles', 4 => '4 Miles', 5 => '5 Miles', 7 => '7 Miles', 10 => '10 Miles'];
                        @endphp
                        <select name="radius" id="radius" class="radius form-control form-control-modern">
                            @foreach($radiuses as $key => $title)
                                <option value="{{ $key }}" @if($key == (request('radius')??5)) selected @endif>{{ $title }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group col-12 col-md-3 mb-3 mb-md-0">
                        <label for="address" style="font-weight: 600; color: #2d3748; margin-bottom: 0.5rem;">
                            <i class="fa fa-location-arrow mr-2"></i>Location
                        </label>
                        <input type="text" name="address" value="{{ request('address') }}" id="address" 
                               class="address form-control form-control-modern" placeholder="Enter location" />
                    </div>
                    
                    <input type="hidden" name="lat" class="lat" value="{{ request('lat') }}" />
                    <input type="hidden" name="long" class="long" value="{{ request('long') }}" />
                    
                    <div class="form-group col-12 col-md-2 mb-3 mb-md-0">
                        <label for="experience" style="font-weight: 600; color: #2d3748; margin-bottom: 0.5rem;">
                            <i class="fa fa-star mr-2"></i>Experience
                        </label>
                        <select name="experience[]" id="experience" multiple class="experience experience_filter form-control form-control-modern" 
                                style="min-height: 45px; padding: 0.5rem;" size="4">
                            @php
                                $selectedExperiences = request('experience', []);
                                if (!is_array($selectedExperiences)) {
                                    $selectedExperiences = $selectedExperiences ? [$selectedExperiences] : [];
                                }
                            @endphp
                            @foreach($experiences as $experience)
                                <option value="{{ $experience->id }}" data-type="{{ $experience->role_id }}"
                                        @if(in_array($experience->id, $selectedExperiences)) selected @endif
                                        @if(request('type') && request('type') != $experience->role_id) class="d-none" @endif>
                                    {{ ucfirst($experience->title) }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 0.75rem; display: block; margin-top: 0.25rem;">
                            <i class="fa fa-info-circle"></i> Hold Ctrl/Cmd to select multiple
                        </small>
                    </div>
                    
                    <div class="form-group col-12 col-md-2 mb-3 mb-md-0 d-flex align-items-end">
                        <button class="btn btn-search btn-block" type="submit">
                            <i class="fa fa-search mr-2"></i>Search
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Informational Messages -->
        @if(isset($isClient) && $isClient)
            <div class="alert alert-info mb-4" style="border-radius: 12px; border-left: 4px solid #4299e1;">
                <i class="fa fa-info-circle mr-2"></i> 
                <strong>Note:</strong> You can search and view profiles of all carers, childminders, and housekeepers to find the perfect match for your needs.
            </div>
        @elseif(isset($isAdmin) && $isAdmin)
            <div class="alert alert-warning mb-4" style="border-radius: 12px; border-left: 4px solid #ed8936;">
                <i class="fa fa-eye mr-2"></i> 
                <strong>Admin View:</strong> You can see all service providers (carers, childminders, and housekeepers) on the platform.
            </div>
        @endif

        <!-- Results Header -->
        @if($paginatedUsers->count() > 0)
        <div class="results-header">
            <div class="results-count">
                Found <span>{{ $paginatedUsers->total() }}</span> {{ $paginatedUsers->total() == 1 ? 'provider' : 'providers' }}
                <span style="color: #667eea; font-size: 0.9rem; margin-left: 1rem;">
                    <i class="fa fa-robot"></i> Ranked by AI matching algorithm
                </span>
            </div>
        </div>
        @endif

        <!-- Top 5 Best Matches Section -->
        @php
            $topMatches = collect($paginatedUsers->items())->take(5);
        @endphp
        @if($topMatches->count() > 0 && $paginatedUsers->currentPage() == 1)
        <div class="alert alert-info mb-4" style="border-radius: 12px; border-left: 4px solid #667eea; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);">
            <h5 style="font-weight: 700; color: #667eea; margin-bottom: 0.5rem;">
                <i class="fa fa-star mr-2"></i>Top 5 Best Matches
            </h5>
            <p style="margin-bottom: 0; color: #4a5568;">
                These providers have been ranked highest based on experience, location proximity, ratings, and verification status.
            </p>
        </div>
        @endif

        <!-- Seller Listings -->
        <div class="row">
            @forelse($paginatedUsers as $index => $user)
                <div class="col-12 col-lg-6">
                    <div class="seller-card" style="position: relative; @if($paginatedUsers->currentPage() == 1 && $index < 5) border: 2px solid #667eea; box-shadow: 0 8px 30px rgba(102, 126, 234, 0.2); @endif">
                        @if($paginatedUsers->currentPage() == 1 && $index < 5)
                            <div style="position: absolute; top: -12px; right: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.25rem 0.75rem; border-radius: 15px; font-size: 0.75rem; font-weight: 700; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); z-index: 10;">
                                <i class="fa fa-trophy mr-1"></i>Top Match #{{ $index + 1 }}
                            </div>
                        @endif
                        <div class="row">
                            <!-- Avatar -->
                            <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                                <img class="seller-avatar-modern"
                                     src="{{ asset($user->profile ?? 'img/undraw_profile.svg') }}"
                                     alt="{{ $user->name ?? '' }}">
                                
                                <!-- Rating -->
                                <div class="rating-display mt-3">
                                    <span class="rating raty readable" data-score="{{ $user->reviews_avg ?? 0 }}"></span>
                                    <span style="color: #718096; font-size: 0.9rem; margin-left: 0.5rem;">
                                        ({{ $user->reviews_count ?? 0 }})
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Details -->
                            <div class="col-12 col-md-8">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h3 style="font-size: 1.5rem; font-weight: 700; color: #2d3748; margin-bottom: 0.75rem;">
                                        {{ $user->name ?? ($user->firstname ?? '') . ' ' . ($user->lastname ?? '') }}
                                    </h3>
                                    @if(isset($user->match_percentage))
                                        <div class="match-score-badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 700; font-size: 0.9rem; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                                            <i class="fa fa-star mr-1"></i>{{ $user->match_percentage }}% Match
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Match Accuracy Details -->
                                @if(isset($user->match_breakdown))
                                <div class="match-breakdown mb-3" style="background: #f7fafc; padding: 0.75rem; border-radius: 8px; font-size: 0.85rem;">
                                    <div class="row text-center">
                                        <div class="col-3">
                                            <div style="font-weight: 600; color: #667eea;">Experience</div>
                                            <div style="color: #718096;">{{ $user->match_breakdown['experience']['percentage'] ?? 0 }}%</div>
                                        </div>
                                        <div class="col-3">
                                            <div style="font-weight: 600; color: #667eea;">Location</div>
                                            <div style="color: #718096;">
                                                @if(isset($user->match_breakdown['location']['distance']))
                                                    {{ number_format($user->match_breakdown['location']['distance'], 1) }} miles
                                                @else
                                                    {{ $user->match_breakdown['location']['percentage'] ?? 0 }}%
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div style="font-weight: 600; color: #667eea;">Ratings</div>
                                            <div style="color: #718096;">{{ $user->match_breakdown['ratings']['percentage'] ?? 0 }}%</div>
                                        </div>
                                        <div class="col-3">
                                            <div style="font-weight: 600; color: #667eea;">Verified</div>
                                            <div style="color: #718096;">{{ $user->match_breakdown['verification']['percentage'] ?? 0 }}%</div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                                <!-- Location & Distance -->
                                <div class="stats-item">
                                    <i class="fa fa-map-marker"></i>
                                    <span>
                                        {{ $user->city ?? 'N/A' }}{{ $user->country ? ', ' . $user->country : '' }}
                                        @if(isset($user->match_breakdown['location']['distance']))
                                            <strong style="color: #667eea;"> • {{ number_format($user->match_breakdown['location']['distance'], 1) }} miles away</strong>
                                        @elseif(request('lat') && request('long') && isset($user->miles))
                                            <strong style="color: #667eea;"> • {{ $user->miles ?? 0 }} miles away</strong>
                                        @endif
                                    </span>
                                </div>
                                
                                <!-- Experience -->
                                @if($user->years_experience)
                                <div class="stats-item">
                                    <i class="fa fa-briefcase"></i>
                                    <span><strong>{{ $user->years_experience }}</strong> years of experience</span>
                                </div>
                                @endif
                                
                                <!-- Badges -->
                                <div class="mb-3">
                                    @if($user->approved ?? false)
                                        <span class="badge-verified">
                                            <i class="fa fa-check-circle"></i>Verified
                                        </span>
                                    @endif
                                    
                                    @if($user->role->seller == true && isset($isAdmin) && $isAdmin)
                                        @if($user->role_id == 3)
                                            @foreach($user->availabilities as $avail)
                                                <span class="badge-price">
                                                    {{ $avail->type->title }}: £{{ $avail->charges }}/hr
                                                </span>
                                            @endforeach
                                        @elseif($user->fee ?? false)
                                            <span class="badge-price">
                                                £{{ $user->fee ?? 10 }}/hr
                                            </span>
                                        @endif
                                    @endif
                                </div>
                                
                                <!-- Info Snippet -->
                                @if($user->info)
                                <p class="info-snippet">
                                    {{ $user->info }}
                                </p>
                                @endif
                                
                                <!-- Video Profile Button -->
                                @if($user->verified_video && \Storage::disk('s3')->exists($user->video))
                                    <button type="button" class="btn btn-sm btn-outline-primary mb-2" 
                                            data-toggle="modal" data-target="#videoModal{{ $user->id }}"
                                            style="border-radius: 50px; padding: 0.5rem 1.25rem;">
                                        <i class="fa fa-video-camera mr-2"></i>Watch Video
                                    </button>
                                @endif
                                
                                <!-- View Profile Button -->
                                <a href="{{ route('seller.show', ['user' => $user->id]) }}" class="btn btn-view-profile">
                                    <i class="fa fa-user mr-2"></i>View Full Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Video Modal -->
                @if($user->verified_video && \Storage::disk('s3')->exists($user->video))
                <div class="modal fade" id="videoModal{{ $user->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
                            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                                <h5 class="modal-title" style="font-weight: 600;">
                                    <i class="fa fa-video-camera mr-2"></i>Video Profile
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.9;">
                                    <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" style="padding: 0;">
                                <video class="w-100" 
                                       src="{{ \Storage::disk('s3')->temporaryUrl($user->video, \Carbon\Carbon::now()->addMinutes(60)) }}" 
                                       controls autoplay style="max-height: 70vh; display: block;"></video>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @empty
                <div class="col-12">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fa fa-search"></i>
                        </div>
                        <h3 style="font-size: 1.5rem; font-weight: 700; color: #2d3748; margin-bottom: 1rem;">
                            No Providers Found
                        </h3>
                        <p style="color: #718096; font-size: 1.1rem; margin-bottom: 2rem;">
                            Try adjusting your search filters to find more results.
                        </p>
                        <a href="{{ route('sellers') }}" class="btn btn-search">
                            <i class="fa fa-refresh mr-2"></i>Clear Filters
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($paginatedUsers->hasPages())
        <div class="row mt-5">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $paginatedUsers->links('app.template.pagination') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
