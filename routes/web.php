<?php

use App\Http\Controllers\BlotterController;
use App\Http\Controllers\DemoWorkspaceController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('/login', [DemoWorkspaceController::class, 'login'])->name('login');
Route::post('/login', [DemoWorkspaceController::class, 'enter'])->name('demo.enter');
Route::get('/dashboard', [DemoWorkspaceController::class, 'dashboard'])->name('dashboard');
Route::post('/logout', [DemoWorkspaceController::class, 'leave'])->name('logout');
Route::get('/', [DemoWorkspaceController::class, 'dashboard'])->name('dashboard');
Route::redirect('/dashboard', '/');
Route::resource('blotters', BlotterController::class);