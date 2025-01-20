<?php

use App\Http\Controllers\{
    HomeController,
    CardController,
    PackController,
    ProfileController,
    PulseController,
    ImageController
};
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');

    Route::prefix('/cards')->group(function () {
        Route::get('/', [CardController::class, 'index'])->name('cards.index');
        Route::get('/{cardId}', [CardController::class, 'show'])->name('cards.show');
    });

    Route::prefix('/packs')->group(function () {
        Route::get('/', [PackController::class, 'index'])->name('packs.index');
        Route::post('/{packId}/open', [PackController::class, 'open'])->name('packs.open');
    });

    Route::prefix('/pulse')->group(function () {
        Route::get('/', [PulseController::class, 'index'])->name('pulse.index');
        Route::post('/claim', [PulseController::class, 'claim'])->name('pulse.claim');
        Route::get('/status', [PulseController::class, 'checkStatus'])->name('pulse.status');
    });

    Route::prefix('/images')->group(function () {
        Route::get('/', [ImageController::class, 'gallery'])->name('images.gallery');
        Route::get('/create', [ImageController::class, 'create'])->name('images.create');
        Route::post('/generate', [ImageController::class, 'generate'])->name('images.generate');
        Route::get('/status/{taskId}', [ImageController::class, 'status'])->name('images.status');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
