<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SavingTransactionController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));
Route::get('/dashboard', DashboardController::class)->name('dashboard');
Route::resource('students', StudentController::class);
Route::get('/transactions/create', [SavingTransactionController::class, 'create'])->name('transactions.create');
Route::post('/transactions', [SavingTransactionController::class, 'store'])->name('transactions.store');
Route::get('/transactions', [SavingTransactionController::class, 'index'])->name('transactions.index');
