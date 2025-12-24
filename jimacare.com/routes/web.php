<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|/
*/

Route::get('/email_testing', [\App\Http\Controllers\EmailtestingController::class, 'email'])->name('email_testing');


Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Dual Navigation Routes
Route::get('/for-clients', [\App\Http\Controllers\HomeController::class, 'forClients'])->name('for.clients');
Route::get('/for-providers', [\App\Http\Controllers\HomeController::class, 'forProviders'])->name('for.providers');

// Compliance & Certification Tracking
Route::middleware(['auth'])->group(function () {
    Route::get('/compliance', [\App\Http\Controllers\ComplianceController::class, 'index'])->name('compliance.index');
    Route::get('/compliance/alerts', [\App\Http\Controllers\ComplianceController::class, 'alerts'])->name('compliance.alerts');
    Route::post('/compliance/document/{document}/update-expiry', [\App\Http\Controllers\ComplianceController::class, 'updateExpiry'])->name('compliance.update-expiry');
});

// Analytics Dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/export', [\App\Http\Controllers\AnalyticsController::class, 'export'])->name('analytics.export');
});

Route::get('/about', [\App\Http\Controllers\HomeController::class, 'about'])->name('about');
// refrence routes ..
Route::get('reference/cancel/{id}/{reference}', [\App\Http\Controllers\RefreeController::class, 'cancelReference'])->name('reference.cancel');
Route::get('reference/confirm/{id}/{reference}', [\App\Http\Controllers\RefreeController::class, 'confirmReference'])->name('reference.confirm');
Route::post('reference/update', [\App\Http\Controllers\RefreeController::class, 'updateReference'])->name('reference.update');
Route::view('/reference/edit','app.pages.reference.edit');

Route::get('/privacy-policy', [\App\Http\Controllers\HomeController::class, 'privacyPolicy'])->name('privacyPolicy');
Route::get('/terms-condition', [\App\Http\Controllers\HomeController::class, 'termsCondition'])->name('termsCondition');
Route::get('/cookie-policy', [\App\Http\Controllers\HomeController::class, 'cookiePolicy'])->name('cookiePolicy');

Route::get('/childcare-during-pendamic', [\App\Http\Controllers\HomeController::class, 'childcareDuringPendamic'])->name('childcareDuringPendamic');
Route::get('/crona-virus-update', [\App\Http\Controllers\HomeController::class, 'cronaVirusUpdate'])->name('cronaVirusUpdate');
Route::get('/self-employed-carers', [\App\Http\Controllers\HomeController::class, 'selfEmployedCarers'])->name('selfEmployedCarers');

Route::get('/blog', [\App\Http\Controllers\PostController::class, 'index'])->name('blog');
Route::get('/post/{post}', [\App\Http\Controllers\PostController::class, 'show'])->name('post');
Route::get('/invoice/edit', [\App\Http\Controllers\HomeController::class, 'editInvoice']);
Route::get('/invoice/show', [\App\Http\Controllers\HomeController::class, 'showInvoice']);
Route::get('/hiring/create', [\App\Http\Controllers\HomeController::class, 'createHiring']);
Route::get('/helpdesk', [\App\Http\Controllers\FaqController::class, 'index'])->name('helpdesk');
Route::get('/hiring-board', [\App\Http\Controllers\HomeController::class, 'hiringBoard']);
Route::get('/hourly-care', [\App\Http\Controllers\HomeController::class, 'hourlyCare']);
Route::get('/things-to-know', [\App\Http\Controllers\HomeController::class, 'howItWorks'])->name('how-it-works');

Route::get('/jobs', [\App\Http\Controllers\ContractController::class, 'index'])->name('contract.index');
Route::match(['get', 'post'], '/post-a-job/{type?}', [\App\Http\Controllers\ContractController::class, 'create'])->name('contract.create');
Route::post('post-a-job/{type}', [\App\Http\Controllers\ContractController::class, 'store'])->name('contract.store');
Route::get('/job/{contract}', [\App\Http\Controllers\ContractController::class, 'show'])->name('contract.show');
Route::get('/job/{contract}/edit', [\App\Http\Controllers\ContractController::class, 'edit'])->name('contract.edit');
Route::put('/job/{contract}', [\App\Http\Controllers\ContractController::class, 'update'])->name('contract.update');
Route::delete('/job/{contract}', [\App\Http\Controllers\ContractController::class, 'destroy'])->name('contract.destroy');

Route::get('/job/apply', [\App\Http\Controllers\HomeController::class, 'jobApply']);
Route::get('/job/board', [\App\Http\Controllers\HomeController::class, 'jobBoard']);
Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'store'])->name('profile.store');

Route::get('/upload-photo', [\App\Http\Controllers\ProfileController::class, 'photo'])->name('photo');
Route::post('/upload-photo', [\App\Http\Controllers\ProfileController::class, 'storePhoto'])->middleware('throttle:uploads');

Route::get('/upload-video', [\App\Http\Controllers\ProfileController::class, 'video'])->name('video');
Route::post('/upload-video', [\App\Http\Controllers\ProfileController::class, 'storeVideo'])->middleware('throttle:uploads');


Route::get('/my-ratings', [\App\Http\Controllers\ProfileController::class, 'ratings'])->name('ratings');
Route::post('/my-ratings', [\App\Http\Controllers\ProfileController::class, 'storeRatings']);
Route::get('/my-documents', [\App\Http\Controllers\ProfileController::class, 'documents'])->name('documents');
Route::get('/document/{document}', [\App\Http\Controllers\DocumentController::class, 'destroy'])->name('document.destroy');
Route::get('/doc/{document}', [\App\Http\Controllers\DocumentController::class, 'show'])->name('document.show');
Route::post('/my-documents', [\App\Http\Controllers\ProfileController::class, 'storeDocuments']);

Route::get('team', [\App\Http\Controllers\HomeController::class, 'team'])->name('team');
Route::get('sellers', [\App\Http\Controllers\UserController::class, 'index'])->name('sellers')->middleware('auth');
Route::get('/export-users', [\App\Http\Controllers\Admin\UserController::class, 'exportUsers'])->name('users.export')->middleware(['auth', 'admin']);
Route::get('/export-orders', [\App\Http\Controllers\Admin\OrderController::class, 'exportOrders'])->name('orders.export');

Route::get('profile/{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('seller.show')->middleware('auth');

Route::get('/inbox', [\App\Http\Controllers\InboxController::class, 'index'])->name('inbox');
Route::get('/inbox/{user}', [\App\Http\Controllers\InboxController::class, 'show'])->name('inbox.show')->where('user', '[0-9]+');
// Redirect old GET /message/{user} links to inbox
Route::get('/message/{user}', [\App\Http\Controllers\InboxController::class, 'show'])->name('message.get')->where('user', '[0-9]+');
Route::post('/message/{user}', [\App\Http\Controllers\MessageController::class, 'store'])->name('message');

Route::post('/message-invoice/{user}', [\App\Http\Controllers\MessageController::class, 'invoice'])->name('message.invoice');

Route::get('invoice/{invoice}/pay', [\App\Http\Controllers\InvoiceController::class, 'pay'])->name('invoice.pay');
Route::post('invoice/{invoice}/processPayment', [\App\Http\Controllers\InvoiceController::class, 'processPayment'])->name('invoice.processPayment');
Route::get('thankyou', [\App\Http\Controllers\InvoiceController::class, 'thankyou'])->name('invoice.thankyou');
Route::get('invoice/{invoice}/reject', [\App\Http\Controllers\InvoiceController::class, 'reject'])->name('invoice.reject');
Route::get('invoice/{invoice}/cancel', [\App\Http\Controllers\InvoiceController::class, 'cancel'])->name('invoice.cancel');


Route::get('orders', [\App\Http\Controllers\OrderController::class, 'index'])->name('order.index');
Route::get('order/{order}', [\App\Http\Controllers\OrderController::class, 'show'])->name('order.show');
Route::post('order/{order}/complete', [\App\Http\Controllers\OrderController::class, 'complete'])->name('order.complete');
Route::post('order/{order}/revision', [\App\Http\Controllers\OrderController::class, 'revision'])->name('order.revision');
Route::post('order/{order}/review', [\App\Http\Controllers\OrderController::class, 'review'])->name('order.review');
Route::post('order/{order}/submit', [\App\Http\Controllers\OrderController::class, 'submit'])->name('order.submit');

// ============================================
// NEW FEATURES: Job Applications, Timesheets, Notifications
// ============================================

Route::middleware(['auth'])->group(function () {
    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
    
    // Job Applications (for Clients - view applications to their jobs)
    Route::get('/job-applications', [\App\Http\Controllers\JobApplicationController::class, 'index'])->name('job-applications.index');
    Route::get('/job/{contract}/applications', [\App\Http\Controllers\JobApplicationController::class, 'viewApplications'])->name('job-applications.view');
    Route::post('/job-application/{application}/accept', [\App\Http\Controllers\JobApplicationController::class, 'accept'])->name('job-applications.accept');
    Route::post('/job-application/{application}/reject', [\App\Http\Controllers\JobApplicationController::class, 'reject'])->name('job-applications.reject');
    
    // Repost Job (for Clients)
    Route::post('/job/{contract}/repost', [\App\Http\Controllers\ContractController::class, 'repost'])->name('contract.repost');
    
    // Job Applications (for Carers - apply to jobs, view their applications)
    Route::get('/my-applications', [\App\Http\Controllers\JobApplicationController::class, 'myApplications'])->name('my-applications.index');
    Route::post('/job/{contract}/apply', [\App\Http\Controllers\JobApplicationController::class, 'apply'])->name('job.apply');
    Route::get('/job/{contract}/accept', [\App\Http\Controllers\JobApplicationController::class, 'acceptJobGet'])->name('job.accept.get');
    Route::post('/job/{contract}/accept', [\App\Http\Controllers\JobApplicationController::class, 'acceptJob'])->name('job.accept');
    Route::post('/job/{contract}/reject', [\App\Http\Controllers\JobApplicationController::class, 'rejectJob'])->name('job.reject');
    Route::post('/job/{contract}/invite', [\App\Http\Controllers\JobApplicationController::class, 'invite'])->name('job.invite');
    Route::post('/job-invitation/{invitation}/accept', [\App\Http\Controllers\JobApplicationController::class, 'acceptInvitation'])->name('job-invitation.accept');
    Route::post('/job-invitation/{invitation}/reject', [\App\Http\Controllers\JobApplicationController::class, 'rejectInvitation'])->name('job-invitation.reject');
    Route::post('/job-application/{application}/withdraw', [\App\Http\Controllers\JobApplicationController::class, 'withdraw'])->name('job-applications.withdraw');
    
    // Timesheets (for Carers)
    Route::get('/carer/timesheets', [\App\Http\Controllers\TimesheetController::class, 'carerIndex'])->name('carer.timesheets');
    Route::post('/timesheet/clock-in', [\App\Http\Controllers\TimesheetController::class, 'clockIn'])->name('timesheet.clockIn');
    Route::post('/timesheet/{timesheet}/clock-out', [\App\Http\Controllers\TimesheetController::class, 'clockOut'])->name('timesheet.clockOut');
    Route::post('/timesheet/{timesheet}/note', [\App\Http\Controllers\TimesheetController::class, 'addNote'])->name('timesheet.addNote');
    
    // Timesheets (for Clients)
    Route::get('/client/timesheets', [\App\Http\Controllers\TimesheetController::class, 'clientIndex'])->name('client.timesheets');
    Route::post('/timesheet/{timesheet}/approve', [\App\Http\Controllers\TimesheetController::class, 'approve'])->name('timesheet.approve');
    Route::post('/timesheet/{timesheet}/dispute', [\App\Http\Controllers\TimesheetController::class, 'dispute'])->name('timesheet.dispute');
    Route::post('/timesheet/{timesheet}/cancel', [\App\Http\Controllers\TimesheetController::class, 'cancel'])->name('timesheet.cancel');
    
    // Timesheet Export (for all roles)
    Route::get('/timesheets/export', [\App\Http\Controllers\TimesheetController::class, 'export'])->name('timesheets.export');
    
    // Timesheet Payments (for Clients)
    Route::get('/timesheet-payments', [\App\Http\Controllers\TimesheetPaymentController::class, 'index'])->name('timesheet-payments.index');
    Route::get('/timesheet-payments/{payment}', [\App\Http\Controllers\TimesheetPaymentController::class, 'show'])->name('timesheet-payments.show');
    Route::post('/timesheet-payments/generate', [\App\Http\Controllers\TimesheetPaymentController::class, 'generateAndSendPayments'])->name('timesheet-payments.generate');
    
    // Location Tracking (for Carers)
    Route::post('/location/update', [\App\Http\Controllers\LocationController::class, 'update'])->name('location.update');
    Route::get('/location/{carer}', [\App\Http\Controllers\LocationController::class, 'getLocation'])->name('location.get');
});

Auth::routes();

Route::get('/payment/success', [\App\Http\Controllers\TimesheetPaymentController::class, 'success'])->name('payment.success');

// Video Calls (using web auth for session-based authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/video-call', [\App\Http\Controllers\Api\VideoCallController::class, 'show'])->name('video.show');
    Route::post('/video-call/{user}', [\App\Http\Controllers\Api\VideoCallController::class, 'initiate'])->name('video.call');
    Route::get('/video-call/join/{room}', [\App\Http\Controllers\Api\VideoCallController::class, 'join'])->name('video.join');
    Route::post('/video-call/end/{room}', [\App\Http\Controllers\Api\VideoCallController::class, 'end'])->name('video.end');
    Route::post('/video-call/decline/{room}', [\App\Http\Controllers\Api\VideoCallController::class, 'decline'])->name('video.decline');
    Route::get('/video-call/diagnose', [\App\Http\Controllers\Api\VideoCallController::class, 'diagnose'])->name('video.diagnose');
});

Route::get('/email/verify', function () {
	return view('app.pages.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/phone/verify', function () {
	return view('app.pages.verify-phone');
})->middleware('auth')->name('verification.phone');

Route::post('/verify/phone/otp', [\App\Http\Controllers\HomeController::class, 'verify'])->middleware('auth')->name('verify.phone.otp');

Route::get('/resend/otp', [\App\Http\Controllers\HomeController::class, 'resendOtp'])->middleware('auth')->name('resend.phone.otp');

// Password change (for users with temporary password)
Route::get('/password/change', [\App\Http\Controllers\Auth\ChangePasswordController::class, 'showChangePasswordForm'])->middleware('auth')->name('password.change');
Route::post('/password/change', [\App\Http\Controllers\Auth\ChangePasswordController::class, 'changePassword'])->middleware('auth')->name('password.change.submit');

Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
	$request->fulfill();

	return redirect('/profile');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
	$request->user()->sendEmailVerificationNotification();

	return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');



Route::get('register/{type}', [\App\Http\Controllers\Auth\RegisterController::class, 'registrationForm'])->name('register.type');




Route::resources([
	'user' => 'UserController',
	//'contract'  => 'ContractController',
	'type' => 'TypeController',
]);

Route::prefix('dashboard')->middleware(['auth', 'admin'])->group(function () {
	Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

	Route::name('dashboard.')->group(function () {

		Route::resources([
			'language' => \App\Http\Controllers\Admin\LanguageController::class,

			'type' => \App\Http\Controllers\Admin\TypeController::class,
			'education' => \App\Http\Controllers\Admin\EducationController::class,
			'experience' => \App\Http\Controllers\Admin\ExperienceController::class,
			'skill' => \App\Http\Controllers\Admin\SkillController::class,
			'interest' => \App\Http\Controllers\Admin\InterestController::class,

			'post' => \App\Http\Controllers\Admin\PostController::class,
			'faq' => \App\Http\Controllers\Admin\FaqController::class,

			'user' => \App\Http\Controllers\Admin\UserController::class,
			//'job'          =>  \App\Http\Controllers\Admin\ContractController::class,
		]);

		Route::post('/user/{user}/status', [\App\Http\Controllers\Admin\UserController::class, 'status'])->name('user.status');
		Route::post('/user/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('user.reset-password');
		Route::post('/users/bulk-status', [\App\Http\Controllers\Admin\UserController::class, 'bulkStatus'])->name('user.bulk-status');
		Route::post('/users/bulk-delete', [\App\Http\Controllers\Admin\UserController::class, 'bulkDelete'])->name('user.bulk-delete');

		Route::get('/admins', [\App\Http\Controllers\Admin\UserController::class, 'admins'])->name('admin.index');
		Route::get('/jobs', [\App\Http\Controllers\Admin\ContractController::class, 'index'])->name('contract.index');
		Route::get('{type}/post-a-job', [\App\Http\Controllers\Admin\ContractController::class, 'create'])->name('contract.create');
		Route::post('{type}/post-a-job', [\App\Http\Controllers\Admin\ContractController::class, 'store'])->name('contract.store');
		Route::get('/job/{contract}', [\App\Http\Controllers\Admin\ContractController::class, 'show'])->name('contract.show');
		Route::get('/job/{contract}/edit', [\App\Http\Controllers\Admin\ContractController::class, 'edit'])->name('contract.edit');
		Route::put('/job/{contract}', [\App\Http\Controllers\Admin\ContractController::class, 'update'])->name('contract.update');
		Route::delete('/job/{contract}', [\App\Http\Controllers\Admin\ContractController::class, 'destroy'])->name('contract.destroy');
		Route::post('/job/{contract}/status', [\App\Http\Controllers\Admin\ContractController::class, 'status'])->name('contract.status');
		Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('order.index');
		
		// Timesheets Management
		Route::get('/timesheets', [\App\Http\Controllers\Admin\TimesheetController::class, 'index'])->name('timesheets.index');
		Route::get('/timesheets/{timesheet}', [\App\Http\Controllers\Admin\TimesheetController::class, 'show'])->name('timesheets.show');
		Route::post('/timesheets/{timesheet}/approve', [\App\Http\Controllers\Admin\TimesheetController::class, 'approve'])->name('timesheets.approve');
		Route::post('/timesheets/{timesheet}/dispute', [\App\Http\Controllers\Admin\TimesheetController::class, 'dispute'])->name('timesheets.dispute');
		Route::post('/timesheets/{timesheet}/cancel', [\App\Http\Controllers\Admin\TimesheetController::class, 'cancel'])->name('timesheets.cancel');
		
		// Timesheet Payments Management (Admin)
		Route::get('/timesheet-payments', [\App\Http\Controllers\Admin\TimesheetPaymentController::class, 'index'])->name('timesheet-payments.index');
		Route::get('/timesheet-payments/{payment}', [\App\Http\Controllers\Admin\TimesheetPaymentController::class, 'show'])->name('timesheet-payments.show');
		Route::post('/timesheet-payments/generate', [\App\Http\Controllers\Admin\TimesheetPaymentController::class, 'generateAndSendPayments'])->name('timesheet-payments.generate');
		
		// Job Applications Management
		Route::get('/job-applications', [\App\Http\Controllers\Admin\JobApplicationController::class, 'index'])->name('job-applications.index');
		Route::get('/job-applications/{application}', [\App\Http\Controllers\Admin\JobApplicationController::class, 'show'])->name('job-applications.show');
	});
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index']);
