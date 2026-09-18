<?php

use App\Http\Controllers\BlotterController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\OfficialController;
use App\Http\Controllers\ResidentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::redirect('/dashboard', '/');
Route::resource('residents', ResidentController::class);
Route::resource('households', HouseholdController::class);
Route::resource('blotters', BlotterController::class);
Route::resource('officials', OfficialController::class);
Route::resource('certificates', CertificateController::class);
