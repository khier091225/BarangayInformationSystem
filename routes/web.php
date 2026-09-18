<?php

use App\Http\Controllers\BlotterController;
use App\Http\Controllers\DemoWorkspaceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DemoWorkspaceController::class, 'dashboard'])->name('dashboard');
Route::redirect('/dashboard', '/');
Route::resource('blotters', BlotterController::class);
