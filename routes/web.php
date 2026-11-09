<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RatingController;
use Illuminate\Support\Facades\Route;

// Halaman utama
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Halaman untuk pengguna yang belum login
Route::middleware('guest')->group(function () {

    // Registrasi
    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.store');

    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.attempt');
});

// Logout untuk pengguna yang sudah login
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// CRUD proyek GarudaHub
Route::resource('projects', ProjectController::class)
    ->middleware('auth');

// Developer mengirim penawaran
Route::post('/projects/{project}/bids', [BidController::class, 'store'])
    ->middleware('auth')
    ->name('bids.store');

// Klien memilih pemenang lelang
Route::post('/projects/{project}/bids/{bid}/winner', [BidController::class, 'selectWinner'])
    ->middleware('auth')
    ->name('bids.winner');

// Klien memberikan rating kepada developer pemenang
Route::post('/projects/{project}/rating', [RatingController::class, 'store'])
    ->middleware('auth')
    ->name('ratings.store');
