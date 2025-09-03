<?php
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeminiController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KecelakaanController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// ========================
// HALAMAN LOGIN & AUTH
// ========================

// Form login (hapus guest middleware)
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');

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
// ROUTE HALAMAN ROOT
// ========================
Route::get('/', function () {
    return redirect()->route('login');
});

// ========================
// ROUTE UNTUK HALAMAN DASHBOARD
// ========================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// ========================
// ROUTE UNTUK PROFILE
// ========================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ========================
// ROUTE UNTUK ADMIN
// ========================
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/ai', [GeminiController::class, 'show'])->name('gemini.show');
    Route::post('/ai', [GeminiController::class, 'ask'])->name('gemini.ask');
    Route::get('/master', [MasterController::class, 'index'])->name('master');
});

// ========================
// ROUTE UNTUK DATA KECELAKAAN
// ========================
Route::middleware('auth')->group(function () {
    Route::resource('kecelakaan', KecelakaanController::class)->except(['edit']);
    Route::post('/kecelakaan/store', [KecelakaanController::class, 'store'])->name('kecelakaan.store');
    Route::post('/kecelakaan/upload', [KecelakaanController::class, 'upload'])->name('kecelakaan.upload');
    Route::put('/kecelakaan/{kecelakaan}', [KecelakaanController::class, 'update'])->name('kecelakaan.update');
});

// ========================
// ROUTE UNTUK SEARCH IMAGE VIA SERPAPI
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
