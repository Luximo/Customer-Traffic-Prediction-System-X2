<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PredictionTestController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\ExportController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/test-predict', [PredictionTestController::class, 'test'])->name('predict.test');
    Route::get('/predictions', [PredictionController::class, 'index'])->name('predictions.index');
    Route::get('/predictions/export', [ExportController::class, 'export'])->name('predictions.export'); // ← moved in
});

require __DIR__ . '/auth.php';
