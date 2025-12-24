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
|
*/

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/about', [\App\Http\Controllers\HomeController::class, 'about'])->name('about');

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
Route::post('/upload-photo', [\App\Http\Controllers\ProfileController::class, 'storePhoto']);

Route::get('/upload-video', [\App\Http\Controllers\ProfileController::class, 'video'])->name('video');
Route::post('/upload-video', [\App\Http\Controllers\ProfileController::class, 'storeVideo']);


Route::get('/my-ratings', [\App\Http\Controllers\ProfileController::class, 'ratings'])->name('ratings');
Route::post('/my-ratings', [\App\Http\Controllers\ProfileController::class, 'storeRatings']);
Route::get('/my-documents', [\App\Http\Controllers\ProfileController::class, 'documents'])->name('documents');
Route::post('/my-documents', [\App\Http\Controllers\ProfileController::class, 'storeDocuments']);

Route::get('team', [\App\Http\Controllers\HomeController::class, 'team'])->name('team');
Route::get('sellers', [\App\Http\Controllers\UserController::class, 'index'])->name('sellers');
Route::get('profile/{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('seller.show');

Route::get('/inbox', [\App\Http\Controllers\InboxController::class, 'index'])->name('inbox');
Route::get('/inbox/{user}', [\App\Http\Controllers\InboxController::class, 'show'])->name('inbox.show');
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

Auth::routes();

Route::get('/email/verify', function () {
	return view('app.pages.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
	$request->fulfill();

	return redirect('/profile');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
	$request->user()->sendEmailVerificationNotification();

	return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');



Route::get('register/{type}', [\App\Http\Controllers\Auth\RegisterController::class, 'registrationForm'])->name('register.type');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index']);


Route::resources([
	'user'      => 'UserController',
	//'contract'  => 'ContractController',
	'type'      => 'TypeController',
]);

Route::prefix('dashboard')->middleware(['auth', 'admin'])->group(function () {
	Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

	Route::name('dashboard.')->group(function () {

		Route::resources([
			'language'      => \App\Http\Controllers\Admin\LanguageController::class,

			'type'          => \App\Http\Controllers\Admin\TypeController::class,
			'education'    => \App\Http\Controllers\Admin\EducationController::class,
			'experience'    => \App\Http\Controllers\Admin\ExperienceController::class,
			'skill'         => \App\Http\Controllers\Admin\SkillController::class,
			'interest'      => \App\Http\Controllers\Admin\InterestController::class,

			'post'          => \App\Http\Controllers\Admin\PostController::class,
			'faq'           => \App\Http\Controllers\Admin\FaqController::class,

			'user'         => \App\Http\Controllers\Admin\UserController::class,
			//'job'          =>  \App\Http\Controllers\Admin\ContractController::class,
		]);

		Route::post('/user/{user}/status', [\App\Http\Controllers\Admin\UserController::class, 'status'])->name('user.status');

		Route::get('/jobs', [\App\Http\Controllers\Admin\ContractController::class, 'index'])->name('contract.index');
		Route::get('{type}/post-a-job', [ \App\Http\Controllers\Admin\ContractController::class, 'create'])->name('contract.create');
		Route::post('{type}/post-a-job', [ \App\Http\Controllers\Admin\ContractController::class, 'store'])->name('contract.store');
		Route::get('/job/{contract}', [\App\Http\Controllers\Admin\ContractController::class, 'show'])->name('contract.show');
		Route::get('/job/{contract}/edit', [\App\Http\Controllers\Admin\ContractController::class, 'edit'])->name('contract.edit');
		Route::put('/job/{contract}', [\App\Http\Controllers\Admin\ContractController::class, 'update'])->name('contract.update');
		Route::delete('/job/{contract}', [\App\Http\Controllers\Admin\ContractController::class, 'destroy'])->name('contract.destroy');
		Route::post('/job/{contract}/status', [\App\Http\Controllers\Admin\ContractController::class, 'status'])->name('contract.status');
		Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('order.index');
	});
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
