@extends('app.template.layout')

@section('content')
<style>
    .profile-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 3rem 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    
    .profile-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 5px solid rgba(255,255,255,0.3);
        object-fit: cover;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    
    .profile-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .profile-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }
    
    .badge-modern {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
        margin: 0.25rem;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
    }
    
    .badge-modern i {
        margin-right: 0.5rem;
    }
    
    .skill-tag {
        display: inline-block;
        padding: 0.6rem 1.2rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 500;
        margin: 0.4rem 0.4rem 0.4rem 0;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }
    
    .skill-tag:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    
    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 3px solid #667eea;
        display: inline-block;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #f7fafc;
        border-radius: 12px;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
    }
    
    .info-item:hover {
        background: #edf2f7;
        transform: translateX(5px);
    }
    
    .info-item i {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        margin-right: 1rem;
        font-size: 1.1rem;
    }
    
    .btn-modern {
        padding: 0.875rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    
    .btn-primary-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
    }
    
    .btn-primary-modern:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        color: white;
    }
    
    .btn-video-call {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        border: none;
        color: white;
        padding: 0.875rem 1.75rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }
    
    .btn-video-call::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.25);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .btn-video-call:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .btn-video-call:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 8px 25px rgba(72, 187, 120, 0.5);
        color: white;
    }
    
    .btn-video-call:active {
        transform: translateY(-1px) scale(1.02);
    }
    
    .btn-video-call i {
        margin-right: 0.5rem;
        font-size: 1.15rem;
        position: relative;
        z-index: 1;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }
    
    .btn-video-call span {
        position: relative;
        z-index: 1;
    }
    
    .btn-video-call:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    .btn-video-call:disabled i {
        animation: none;
    }
    
    .review-card {
        background: white;
        border-left: 4px solid #667eea;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    
    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .availability-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
    
    .day-card {
        background: #f7fafc;
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .day-card.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .time-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0.5rem;
    }
    
    .time-table th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem;
        border-radius: 10px;
        font-weight: 600;
        text-align: center;
    }
    
    .time-table td {
        background: #f7fafc;
        padding: 1rem;
        border-radius: 10px;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .time-table td.available {
        background: #c6f6d5;
        color: #22543d;
    }
    
    .time-table td.unavailable {
        background: #fed7d7;
        color: #742a2a;
    }
    
    .reference-card {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        border-left: 5px solid #667eea;
    }
    
    @media (max-width: 768px) {
        .profile-hero {
            padding: 2rem 1.5rem;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
        }
        
        .availability-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="pt-5 pb-5" style="background: #f7fafc; min-height: 100vh;">
    <div class="container">
        <!-- Hero Section -->
        <div class="profile-hero">
            <div class="row align-items-center">
                <div class="col-12 col-md-3 text-center mb-4 mb-md-0">
                    <img class="profile-avatar"
                        src="{{ asset($user->profile ?? 'img/undraw_profile.svg') }}"
                        alt="{{ $user->firstname ?? '' }} {{ $user->lastname[0] ?? '' }}">
                </div>
                <div class="col-12 col-md-6 text-center text-md-left">
                    <h1 class="mb-3" style="font-size: 2.5rem; font-weight: 700;">
                        {{ $user->firstname ?? '' }} {{ $user->lastname ?? '' }}
                    </h1>
                    <p class="mb-3" style="font-size: 1.1rem; opacity: 0.95;">
                        <i class="fa fa-map-marker mr-2"></i>
                        {{ $user->city ?? 'N/A' }}{{ $user->country ? ', ' . $user->country : '' }}{{ $user->postcode ? ' ' . $user->postcode : '' }}
                    </p>
                    
                    <div class="mb-3">
                        @if ($user->approved ?? false)
                            <span class="badge-modern">
                                <i class="fa fa-check-circle"></i> Verified & Approved
                            </span>
                        @endif
                    </div>
                    
                    @php
                        $hasVideo = false;
                        $videoExists = false;
                        if ($user->video) {
                            $hasVideo = true;
                            try {
                                if (config('filesystems.default') === 's3') {
                                    $videoExists = Storage::disk('s3')->exists($user->video);
                                } else {
                                    $videoExists = file_exists(public_path($user->video)) || Storage::disk('public')->exists($user->video);
                                }
                            } catch (\Exception $e) {
                                $videoExists = !empty($user->video);
                            }
                        }
                    @endphp
                </div>
                <div class="col-12 col-md-3 text-center text-md-right">
                    @if (auth()->check())
                        @if(auth()->id() != $user->id)
                            <a href="{{ route('inbox.show', $user->id) }}" class="btn btn-modern btn-primary-modern btn-block mb-2">
                                <i class="fa fa-comments mr-2"></i> Send Message
                            </a>
                            <button class="btn btn-video-call btn-block mb-2" 
                                    data-user-id="{{ $user->id }}"
                                    data-user-name="{{ $user->firstname ?? '' }} {{ $user->lastname ?? '' }}">
                                <i class="fa fa-video-camera"></i>
                                <span>Start Video Call</span>
                            </button>
                        @else
                            <button class="btn btn-modern btn-secondary btn-block mb-2" disabled>
                                <i class="fa fa-user mr-2"></i> Your Profile
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-modern btn-primary-modern btn-block mb-2">
                            <i class="fa fa-comments mr-2"></i> Send Message
                        </a>
                    @endif
                    
                    @if ($hasVideo && $videoExists)
                        <button type="button" class="btn btn-modern btn-primary-modern btn-block" data-toggle="modal" data-target="#staticBackdrop">
                            <i class="fa fa-video-camera mr-2"></i> Watch Video
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- About Section -->
        @if($user->info)
        <div class="profile-card">
            <h2 class="section-title">About</h2>
            <p style="font-size: 1.1rem; line-height: 1.8; color: #4a5568;">
                {{ $user->info }}
            </p>
        </div>
        @endif

        <div class="row">
            <!-- Left Column -->
            <div class="col-12 col-lg-8">
                <!-- Experience -->
                @if ($user->experiences->count() > 0)
                <div class="profile-card">
                    <h2 class="section-title">
                        <i class="fa fa-briefcase mr-2"></i> Experience
                    </h2>
                    <div>
                        @foreach ($user->experiences as $exp)
                            <span class="skill-tag">{{ $exp->title }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Skills -->
                @if ($user->skills->count() > 0)
                <div class="profile-card">
                    <h2 class="section-title">
                        <i class="fa fa-star mr-2"></i> Skills
                    </h2>
                    <div>
                        @foreach ($user->skills as $skill)
                            <span class="skill-tag">{{ $skill->title }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Qualifications -->
                @if ($user->educations->count() > 0)
                <div class="profile-card">
                    <h2 class="section-title">
                        <i class="fa fa-graduation-cap mr-2"></i> Qualifications
                    </h2>
                    <div>
                        @foreach ($user->educations as $edu)
                            <span class="skill-tag">{{ $edu->title }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Interests -->
                @if ($user->interests->count() > 0)
                <div class="profile-card">
                    <h2 class="section-title">
                        <i class="fa fa-heart mr-2"></i> Interests
                    </h2>
                    <div>
                        @foreach ($user->interests as $interest)
                            <span class="skill-tag">{{ $interest->title }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Availability -->
                @if ($user->availabilities->count() > 0)
                <div class="profile-card">
                    <h2 class="section-title">
                        <i class="fa fa-clock-o mr-2"></i> Available For
                    </h2>
                    <div>
                        @foreach ($user->availabilities as $avail)
                            <span class="skill-tag">{{ $avail->type->title ?? '' }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Working Days -->
                @if ($user->days->count() > 0)
                <div class="profile-card">
                    <h2 class="section-title">
                        <i class="fa fa-calendar mr-2"></i> Working Days
                    </h2>
                    <div class="availability-grid">
                        @foreach ($user->days as $day)
                            <div class="day-card active">
                                <i class="fa fa-check-circle mb-2" style="font-size: 1.5rem;"></i>
                                <div style="font-weight: 600;">{{ $day->title }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Working Time Schedule -->
                @if ($user->time_availables->count() > 0 && isset($days) && isset($time_types))
                <div class="profile-card">
                    <h2 class="section-title">
                        <i class="fa fa-calendar-check-o mr-2"></i> Working Schedule
                    </h2>
                    <div class="table-responsive">
                        <table class="time-table">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    @foreach ($days as $day)
                                        <th>{{ $day->title }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($time_types ?? [] as $time)
                                    <tr>
                                        <td style="font-weight: 600; background: #edf2f7;">{{ $time->title }}</td>
                                        @foreach ($days as $day)
                                            @php
                                                $avail = $user->time_availables
                                                    ->where('type_id', $time->id)
                                                    ->where('day_id', $day->id)
                                                    ->count();
                                            @endphp
                                            <td class="{{ $avail > 0 ? 'available' : 'unavailable' }}">
                                                @if($avail > 0)
                                                    <i class="fa fa-check-circle" style="font-size: 1.2rem;"></i>
                                                @else
                                                    <i class="fa fa-times-circle" style="font-size: 1.2rem;"></i>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Reviews -->
                @if(isset($user->reviews) && $user->reviews->count() > 0)
                <div class="profile-card">
                    <h2 class="section-title">
                        <i class="fa fa-star mr-2"></i> Reviews
                    </h2>
                    <p class="text-muted mb-4" style="font-size: 0.95rem;">
                        Below are the latest reviews. Please note that reviews represent the opinions of JimaCare platform users and not of JimaCare. Clients must carry out their own checks on providers to ensure that they are completely happy before engaging in the use of their services.
                    </p>
                    @foreach ($user->reviews as $review)
                        <div class="review-card">
                            <div class="review-header">
                                <div>
                                    <strong style="font-size: 1.1rem; color: #2d3748;">
                                        {{ $review->client->firstname ?? 'Anonymous' }}
                                    </strong>
                                    <div class="mt-1">
                                        <span class="rating raty readable" data-score="{{ $review->stars ?? 0 }}"></span>
                                    </div>
                                </div>
                                <div class="text-muted" style="font-size: 0.9rem;">
                                    {{ $review->created_at->format('M d, Y') }}
                                </div>
                            </div>
                            <p style="color: #4a5568; line-height: 1.7; margin: 0;">
                                {{ $review->desc ?? 'No description provided.' }}
                            </p>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="col-12 col-lg-4">
                <!-- Quick Info -->
                <div class="profile-card">
                    <h2 class="section-title" style="font-size: 1.3rem;">Quick Info</h2>
                    @if($user->years_experience)
                    <div class="info-item">
                        <i class="fa fa-calendar"></i>
                        <div>
                            <strong>Experience</strong><br>
                            <span style="color: #718096;">{{ $user->years_experience }} years</span>
                        </div>
                    </div>
                    @endif
                    @if($user->gender)
                    <div class="info-item">
                        <i class="fa fa-user"></i>
                        <div>
                            <strong>Gender</strong><br>
                            <span style="color: #718096;">{{ ucfirst($user->gender) }}</span>
                        </div>
                    </div>
                    @endif
                    @if($user->created_at)
                    <div class="info-item">
                        <i class="fa fa-clock-o"></i>
                        <div>
                            <strong>Member Since</strong><br>
                            <span style="color: #718096;">{{ $user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                    @endif
                    @if($user->last_login)
                    <div class="info-item">
                        <i class="fa fa-sign-in"></i>
                        <div>
                            <strong>Last Active</strong><br>
                            <span style="color: #718096;">{{ $user->last_login->diffForHumans() }}</span>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- References -->
                @if ($user->referee1_status == true && $user->reference1->first() != null)
                <div class="reference-card">
                    <h3 style="font-size: 1.3rem; font-weight: 700; color: #2d3748; margin-bottom: 1.5rem;">
                        <i class="fa fa-user-circle mr-2"></i> Reference 1
                    </h3>
                    <h4 style="font-size: 1.2rem; font-weight: 600; color: #4a5568; margin-bottom: 0.5rem;">
                        {{ $user->reference1->first()->first_name ?? '' }}
                    </h4>
                    <p style="color: #718096; margin-bottom: 1rem;">
                        <i class="fa fa-briefcase mr-2"></i>{{ $user->reference1->first()->job_title ?? '' }}
                    </p>
                    <div style="background: white; padding: 1rem; border-radius: 10px; margin-bottom: 1rem;">
                        <strong style="color: #2d3748;">Employment Duration:</strong><br>
                        <span style="color: #4a5568;">
                            <?php
                            $startDate = Carbon\Carbon::parse($user->reference1->first()->from ?? Carbon\Carbon::now());
                            $endDate = Carbon\Carbon::parse($user->reference1->first()->to ?? Carbon\Carbon::now());
                            $period = $startDate->diff($endDate);
                            echo $period->y . ' years and ' . $period->m . ' months';
                            ?>
                        </span>
                    </div>
                    <div style="background: white; padding: 1rem; border-radius: 10px; margin-bottom: 1rem;">
                        <strong style="color: #2d3748;">Safety Issues:</strong><br>
                        <span style="color: #4a5568;">
                            {{ $user->reference1->first()->emp_safety_issue == true ? 'No issues reported' : 'None' }}
                        </span>
                    </div>
                    @if($user->reference1->first()->responsibility)
                    <div style="background: white; padding: 1rem; border-radius: 10px; margin-bottom: 1rem;">
                        <strong style="color: #2d3748;">Responsibilities:</strong><br>
                        <span style="color: #4a5568;">{{ $user->reference1->first()->responsibility }}</span>
                    </div>
                    @endif
                    <div style="background: white; padding: 1rem; border-radius: 10px;">
                        <strong style="color: #2d3748;">Would Employ Again:</strong><br>
                        <span style="color: #4a5568;">
                            {{ $user->reference1->first()->emp_again == true ? 'Yes' : 'No' }}
                        </span>
                    </div>
                </div>
                @endif

                @if ($user->referee2_status == true && $user->reference2->first() != null)
                <div class="reference-card">
                    <h3 style="font-size: 1.3rem; font-weight: 700; color: #2d3748; margin-bottom: 1.5rem;">
                        <i class="fa fa-user-circle mr-2"></i> Reference 2
                    </h3>
                    <h4 style="font-size: 1.2rem; font-weight: 600; color: #4a5568; margin-bottom: 0.5rem;">
                        {{ $user->reference2->first()->first_name ?? '' }}
                    </h4>
                    <p style="color: #718096; margin-bottom: 1rem;">
                        <i class="fa fa-briefcase mr-2"></i>{{ $user->reference2->first()->job_title ?? '' }}
                    </p>
                    <div style="background: white; padding: 1rem; border-radius: 10px; margin-bottom: 1rem;">
                        <strong style="color: #2d3748;">Employment Duration:</strong><br>
                        <span style="color: #4a5568;">
                            <?php
                            $startDate = Carbon\Carbon::parse($user->reference2->first()->from ?? Carbon\Carbon::now());
                            $endDate = Carbon\Carbon::parse($user->reference2->first()->to ?? Carbon\Carbon::now());
                            $period = $startDate->diff($endDate);
                            echo $period->y . ' years and ' . $period->m . ' months';
                            ?>
                        </span>
                    </div>
                    <div style="background: white; padding: 1rem; border-radius: 10px; margin-bottom: 1rem;">
                        <strong style="color: #2d3748;">Safety Issues:</strong><br>
                        <span style="color: #4a5568;">
                            {{ $user->reference2->first()->emp_safety_issue == true ? 'No issues reported' : 'None' }}
                        </span>
                    </div>
                    @if($user->reference2->first()->responsibility)
                    <div style="background: white; padding: 1rem; border-radius: 10px; margin-bottom: 1rem;">
                        <strong style="color: #2d3748;">Responsibilities:</strong><br>
                        <span style="color: #4a5568;">{{ $user->reference2->first()->responsibility }}</span>
                    </div>
                    @endif
                    <div style="background: white; padding: 1rem; border-radius: 10px;">
                        <strong style="color: #2d3748;">Would Employ Again:</strong><br>
                        <span style="color: #4a5568;">
                            {{ $user->reference2->first()->emp_again == true ? 'Yes' : 'No' }}
                        </span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Video Modal -->
@php
    $hasVideo = false;
    $videoExists = false;
    $videoUrl = '';
    if ($user->video) {
        $hasVideo = true;
        try {
            if (config('filesystems.default') === 's3') {
                $videoExists = Storage::disk('s3')->exists($user->video);
                if ($videoExists) {
                    $videoUrl = Storage::disk('s3')->temporaryUrl($user->video, Carbon\Carbon::now()->addMinutes(60));
                }
            } else {
                if (file_exists(public_path($user->video))) {
                    $videoExists = true;
                    $videoUrl = asset($user->video);
                } elseif (Storage::disk('public')->exists($user->video)) {
                    $videoExists = true;
                    $videoUrl = Storage::disk('public')->url($user->video);
                }
            }
        } catch (\Exception $e) {
            if (!empty($user->video)) {
                $videoExists = true;
                $videoUrl = asset($user->video);
            }
        }
    }
@endphp
@if ($hasVideo && $videoExists && $videoUrl)
    <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                    <h5 class="modal-title" id="staticBackdropLabel" style="font-weight: 600;">
                        <i class="fa fa-video-camera mr-2"></i>Video Profile of {{ $user->firstname ?? '' }} {{ $user->lastname[0] ?? '' }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9;">
                        <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 0;">
                    <video class="w-100" src="{{ $videoUrl }}" controls autoplay style="max-height: 70vh; display: block;"></video>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    // Video Call Button Handler
    document.addEventListener('DOMContentLoaded', function() {
        const videoCallButtons = document.querySelectorAll('.btn-video-call');
        videoCallButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                
                if (!userId) {
                    alert('Unable to initiate video call. User ID not found.');
                    return;
                }
                
                // Show loading state
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i>Connecting...';
                this.disabled = true;
                
                // Make API call to initiate video call (using web route for session auth)
                fetch(`/video-call/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirect to video call page or open in new window
                        window.location.href = `/video-call?room=${data.room_id}&token=${data.token}`;
                    } else {
                        alert(data.message || 'Failed to initiate video call. Please try again.');
                        this.innerHTML = originalHTML;
                        this.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Video call error:', error);
                    alert('An error occurred while initiating the video call. Please try again.');
                    this.innerHTML = originalHTML;
                    this.disabled = false;
                });
            });
        });
    });
</script>

@endsection
