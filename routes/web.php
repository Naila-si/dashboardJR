<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeminiController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KecelakaanController;
use App\Http\Controllers\AhliWarisController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// ========================
// HALAMAN LOGIN & AUTH
// ========================

// Form login
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

// Submit login
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Lupa password
Route::get('/forgot-password', function () {
    return view('auth.forgot-password'); // buat file Blade ini
})->name('password.request');

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ========================
// HALAMAN ROOT
// ========================
Route::get('/', fn() => redirect()->route('login'));

// ========================
// DASHBOARD
// ========================
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// ========================
// PROFILE
// ========================
Route::middleware('auth')->prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ========================
// ADMIN ONLY
// ========================
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/ai', [GeminiController::class, 'show'])->name('gemini.show');
    Route::post('/ai', [GeminiController::class, 'ask'])->name('gemini.ask');

    Route::get('/master', [MasterController::class, 'index'])->name('master');
});

// ========================
// AHLI WARIS
// ========================
Route::middleware('auth')->group(function () {
    Route::resource('ahliwaris', AhliWarisController::class)->except(['edit']);
    Route::post('ahliwaris/upload', [AhliWarisController::class, 'upload'])->name('ahliwaris.upload');
});

// ========================
// DATA KECELAKAAN
// ========================
Route::middleware('auth')->group(function () {
    Route::resource('kecelakaan', KecelakaanController::class)->except(['edit']);
    Route::post('kecelakaan/upload', [KecelakaanController::class, 'upload'])->name('kecelakaan.upload');
});


// ========================
// ROUTE UNTUK ACTIVITY
// ========================
// web.php
Route::prefix('kecelakaan/{kecelakaan}')->group(function () {
    Route::get('activities', [ActivityController::class, 'index'])->name('activity.index');
    Route::post('activities', [ActivityController::class, 'store'])->name('activity.store');
    Route::get('activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activity.edit');
    Route::put('activities/{activity}', [ActivityController::class, 'update'])->name('activity.update');
    Route::delete('activities/{activity}', [ActivityController::class, 'destroy'])->name('activity.destroy');
});

// ========================
// SEARCH IMAGE VIA SERPAPI
// ========================
Route::get('/search-image', function (\Illuminate\Http\Request $request) {
    $query = $request->query('query');
    $apiKey = 'ISI_API_KEY_SERPAPI_KAMU';

    $response = Http::get('https://serpapi.com/search', [
        'engine' => 'google',
        'q' => $query,
        'tbm' => 'isch',
        'api_key' => $apiKey,
    ]);

    $data = $response->json();
    $image = $data['images_results'][0]['thumbnail'] ?? null;

    return response()->json(['image' => $image]);
});
