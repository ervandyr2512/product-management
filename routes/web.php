<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProfessionalController as AdminProfessionalController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfessionalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Professional\ScheduleController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\VideoChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/professionals', [ProfessionalController::class, 'index'])->name('professionals.index');
Route::get('/professionals/{professional}', [ProfessionalController::class, 'show'])->name('professionals.show');

Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'contactSubmit'])->name('contact.submit');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::post('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');

    Route::get('/quick-checkout/{schedule}', [PaymentController::class, 'quickCheckout'])->name('payment.quick-checkout');
    Route::post('/quick-checkout/{schedule}/process', [PaymentController::class, 'quickProcess'])->name('payment.quick-process');

    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

    // Review Routes
    Route::get('/appointments/{appointment}/review', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/appointments/{appointment}/review', [ReviewController::class, 'store'])->name('reviews.store');

    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{professional}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // Chat/Messages
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{user}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{user}', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/{user}/fetch', [ChatController::class, 'fetchMessages'])->name('chat.fetch');

    // Video Chat Routes
    Route::prefix('video-chat')->name('video-chat.')->group(function () {
        Route::get('/appointments/{appointment}', [VideoChatController::class, 'show'])->name('show');
        Route::post('/appointments/{appointment}/start', [VideoChatController::class, 'start'])->name('start');
        Route::post('/appointments/{appointment}/end', [VideoChatController::class, 'end'])->name('end');
        Route::post('/appointments/{appointment}/signal', [VideoChatController::class, 'signal'])->name('signal');
    });

    // Professional Schedule Management
    Route::prefix('professional')->name('professional.')->group(function () {
        Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
        Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
        Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
    });
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::resource('users', AdminUserController::class);

    // Professional Management
    Route::resource('professionals', AdminProfessionalController::class)->except(['create', 'store']);
    Route::post('/professionals/{professional}/toggle-status', [AdminProfessionalController::class, 'toggleStatus'])->name('professionals.toggle-status');

    // Settings / Landing Page Management
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    Route::get('/settings/create', [AdminSettingController::class, 'create'])->name('settings.create');
    Route::post('/settings/store', [AdminSettingController::class, 'store'])->name('settings.store');
    Route::delete('/settings/{setting}', [AdminSettingController::class, 'destroy'])->name('settings.destroy');
});

require __DIR__.'/auth.php';
