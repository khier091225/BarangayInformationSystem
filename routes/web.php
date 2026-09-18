<?php

use App\Http\Controllers\DemoWorkspaceController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('/login', [DemoWorkspaceController::class, 'login'])->name('login');
Route::post('/login', [DemoWorkspaceController::class, 'enter'])->name('demo.enter');
Route::get('/dashboard', [DemoWorkspaceController::class, 'dashboard'])->name('dashboard');
Route::post('/logout', [DemoWorkspaceController::class, 'leave'])->name('logout');
