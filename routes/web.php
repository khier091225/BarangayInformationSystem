<?php

use App\Http\Controllers\BlotterController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OfficialController;
use App\Http\Controllers\ResidentController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:5,1')->name('login.store');
});

Route::middleware('staff.session')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::redirect('/dashboard', '/');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::resource('residents', ResidentController::class);
    Route::resource('households', HouseholdController::class);
    Route::resource('blotters', BlotterController::class);
    Route::resource('officials', OfficialController::class)->except(['show']);
    Route::resource('certificates', CertificateController::class)->except(['edit', 'update']);
});
