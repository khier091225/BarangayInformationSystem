<?php

use App\Http\Controllers\BlotterController;
use App\Http\Controllers\DemoWorkspaceController;
use App\Http\Controllers\HouseholdController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DemoWorkspaceController::class, 'dashboard'])->name('dashboard');
Route::redirect('/dashboard', '/');
Route::resource('blotters', BlotterController::class);
Route::resource('households', HouseholdController::class);
