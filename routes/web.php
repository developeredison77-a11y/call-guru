<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\ClientController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\FaqController;
use App\Http\Controllers\Backend\GurujiController;
use App\Http\Controllers\Backend\ListenerController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\TermsAndConditionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware(['auth', 'superadmin'])->prefix('dashboard')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/listeners', [ListenerController::class, 'index'])->name('listeners.index');
    Route::get('/listeners/{listener}/edit', [ListenerController::class, 'edit'])->name('listeners.edit');
    Route::put('/listeners/{listener}', [ListenerController::class, 'update'])->name('listeners.update');
    Route::patch('/listeners/{listener}/status', [ListenerController::class, 'toggleStatus'])->name('listeners.toggle-status');
    Route::delete('/listeners/{listener}', [ListenerController::class, 'destroy'])->name('listeners.destroy');
    Route::get('/gurujis', [GurujiController::class, 'index'])->name('gurujis.index');
    Route::get('/gurujis/{guruji}/edit', [GurujiController::class, 'edit'])->name('gurujis.edit');
    Route::put('/gurujis/{guruji}', [GurujiController::class, 'update'])->name('gurujis.update');
    Route::patch('/gurujis/{guruji}/status', [GurujiController::class, 'toggleStatus'])->name('gurujis.toggle-status');
    Route::delete('/gurujis/{guruji}', [GurujiController::class, 'destroy'])->name('gurujis.destroy');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::patch('/categories/{category}/status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    Route::resource('terms-and-conditions', TermsAndConditionController::class)
        ->parameters(['terms-and-conditions' => 'termsAndCondition'])
        ->except('show');
    Route::patch('/terms-and-conditions/{termsAndCondition}/status', [TermsAndConditionController::class, 'toggleStatus'])->name('terms-and-conditions.toggle-status');
    Route::resource('faqs', FaqController::class)->except('show');
    Route::patch('/faqs/{faq}/status', [FaqController::class, 'toggleStatus'])->name('faqs.toggle-status');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

Route::get('/logout', function (Request $request) {
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout.get');

Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');
