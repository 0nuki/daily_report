<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ゲストユーザー用ルート（未ログイン）
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// 認証済みユーザー用ルート
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // 日報機能（後で実装）
    Route::get('/daily-reports', function () {
        return view('daily-reports.index');
    })->name('daily-reports.index');
    
    Route::get('/daily-reports/create', function () {
        return view('daily-reports.create');
    })->name('daily-reports.create');
});
