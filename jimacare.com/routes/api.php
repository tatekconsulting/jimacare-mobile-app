<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| V1 API Routes (Require Auth)
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->middleware('auth:api')->group(function () {

    // Availability Management
    Route::prefix('availability')->group(function () {
        Route::post('/toggle', [\App\Http\Controllers\Api\AvailabilityApiController::class, 'toggle']);
        Route::get('/status', [\App\Http\Controllers\Api\AvailabilityApiController::class, 'status']);
        Route::get('/nearby', [\App\Http\Controllers\Api\AvailabilityApiController::class, 'nearby']);
    });

    // Push Notifications
    Route::prefix('push')->group(function () {
        Route::post('/subscribe', [\App\Http\Controllers\Api\PushNotificationController::class, 'subscribe']);
        Route::post('/unsubscribe', [\App\Http\Controllers\Api\PushNotificationController::class, 'unsubscribe']);
        Route::get('/status', [\App\Http\Controllers\Api\PushNotificationController::class, 'status']);
        Route::post('/test', [\App\Http\Controllers\Api\PushNotificationController::class, 'test']);
    });

    // Instant Booking
    Route::prefix('booking')->group(function () {
        Route::post('/{user}', [\App\Http\Controllers\Api\BookingApiController::class, 'create']);
        Route::get('/{booking}/status', [\App\Http\Controllers\Api\BookingApiController::class, 'status']);
        Route::post('/{booking}/accept', [\App\Http\Controllers\Api\BookingApiController::class, 'accept']);
        Route::post('/{booking}/decline', [\App\Http\Controllers\Api\BookingApiController::class, 'decline']);
    });

    // Analytics
    Route::prefix('analytics')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\AnalyticsApiController::class, 'dashboard']);
        Route::get('/earnings', [\App\Http\Controllers\Api\AnalyticsApiController::class, 'earnings']);
        Route::get('/performance', [\App\Http\Controllers\Api\AnalyticsApiController::class, 'performance']);
    });

    // Search
    Route::prefix('search')->group(function () {
        Route::get('/carers', [\App\Http\Controllers\Api\SearchApiController::class, 'carers']);
        Route::get('/jobs', [\App\Http\Controllers\Api\SearchApiController::class, 'jobs']);
    });

    // Location Tracking
    Route::prefix('location')->group(function () {
        Route::post('/update', [\App\Http\Controllers\Api\LocationApiController::class, 'update']);
        Route::get('/track/{booking}', [\App\Http\Controllers\Api\LocationApiController::class, 'track']);
        Route::post('/start/{booking}', [\App\Http\Controllers\Api\LocationApiController::class, 'startSharing']);
    });

    // Timesheets with Face Verification (for Mobile Apps)
    Route::prefix('timesheet')->group(function () {
        Route::post('/clock-in', [\App\Http\Controllers\TimesheetController::class, 'clockIn']);
        Route::post('/{timesheet}/clock-out', [\App\Http\Controllers\TimesheetController::class, 'clockOut']);
    });

    // Video Calls
    Route::prefix('video')->group(function () {
        Route::post('/call/{user}', [\App\Http\Controllers\Api\VideoCallController::class, 'initiate']);
        Route::get('/join/{room}', [\App\Http\Controllers\Api\VideoCallController::class, 'join']);
        Route::post('/end/{room}', [\App\Http\Controllers\Api\VideoCallController::class, 'end']);
        Route::post('/decline/{room}', [\App\Http\Controllers\Api\VideoCallController::class, 'decline']);
    });
});

/*
|--------------------------------------------------------------------------
| Public API Routes (No Auth Required)
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->group(function () {
    // Chatbot
    Route::prefix('chatbot')->group(function () {
        Route::get('/init', [\App\Http\Controllers\Api\ChatbotController::class, 'init']);
        Route::post('/message', [\App\Http\Controllers\Api\ChatbotController::class, 'message']);
    });

    // Search filters
    Route::get('/search/filters', [\App\Http\Controllers\Api\SearchApiController::class, 'filterOptions']);
});

/*
|--------------------------------------------------------------------------
| Chatbot Job Posting & ML Matching Routes (Public)
|--------------------------------------------------------------------------
*/
Route::prefix('chatbot')->group(function () {
    Route::get('/job-types', [\App\Http\Controllers\Api\ChatbotJobController::class, 'getJobTypes']);
    Route::get('/days', [\App\Http\Controllers\Api\ChatbotJobController::class, 'getDays']);
    Route::get('/experiences', [\App\Http\Controllers\Api\ChatbotJobController::class, 'getExperiences']);
    Route::post('/recommendations', [\App\Http\Controllers\Api\ChatbotJobController::class, 'getRecommendations']);
    Route::post('/post-job', [\App\Http\Controllers\Api\ChatbotJobController::class, 'postJob']);
});

// Auth check for chatbot
Route::get('/chatbot/auth-check', function () {
    return response()->json([
        'authenticated' => auth()->check(),
        'user' => auth()->check() ? [
            'id' => auth()->user()->id,
            'name' => auth()->user()->firstname
        ] : null
    ]);
});
