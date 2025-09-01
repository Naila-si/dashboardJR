<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TrafficAccidentImportController;
use App\Http\Controllers\GeminiController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\ProfileController;


// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [DashboardController::class, 'index'])->name('dashboardUser');

Route::middleware(['auth', 'admin'])->group(function () {
    
Route::get('/import', [TrafficAccidentImportController::class, 'showImportForm'])->name('traffic-accidents.import-form');
Route::post('/import', [TrafficAccidentImportController::class, 'import'])->name('traffic-accidents.import');
Route::get('/ai', [GeminiController::class, 'show'])->name('gemini.show');
Route::post('/ai', [GeminiController::class, 'ask'])->name('gemini.ask');
Route::get('/master', [MasterController::class, 'index'])->name('master');
});
//Login





Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

use Illuminate\Support\Facades\Http;

Route::get('/search-image', function (\Illuminate\Http\Request $request) {
    $query = $request->query('query');
    $apiKey = 'ISI_API_KEY_SERPAPI_KAMU'; // <-- Ganti ini
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
