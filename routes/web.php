<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Login / logout / confirm-password / password update (Laravel Breeze)
require __DIR__.'/auth.php';

// Every management page requires a logged-in staff member.
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::resource('products', ProductController::class)->except('show');
    Route::resource('customers', CustomerController::class)->except('show');

    Route::resource('staff', UserController::class)->parameters(['staff' => 'user'])->except(['show', 'destroy']);
    // Deleting a staff account asks the user to re-confirm their password (Breeze "password.confirm").
    Route::delete('staff/{user}', [UserController::class, 'destroy'])->middleware('password.confirm')->name('staff.destroy');

    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
});
