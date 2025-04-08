<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ConversionController;
use App\Http\Controllers\FrameController;
use App\Http\Controllers\HomeController;

// Rotas públicas
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rotas de autenticação com Google
Route::get('login/google', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
Route::get('login/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Rotas protegidas por autenticação
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    
    // Rotas para conversões
    Route::resource('conversions', ConversionController::class);
    Route::get('conversions/{conversion}/download', [ConversionController::class, 'downloadZip'])->name('conversions.download');
    Route::get('conversions/{conversion}/frames/{frame}/download', [FrameController::class, 'download'])->name('frames.download');
});

// Rotas de autenticação padrão do Laravel
require __DIR__.'/auth.php';