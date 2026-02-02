<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderLineController;
use App\Http\Controllers\Admin\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Customer dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'password.changed'])->name('dashboard');

// Admin dashboard
Route::get('/admin', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('admin.dashboard');

// Admin customer management
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/customers/deleted', [CustomerController::class, 'deleted'])->name('customers.deleted');
    Route::post('/customers/{id}/restore', [CustomerController::class, 'restore'])->name('customers.restore');
    Route::delete('/customers/{id}/force', [CustomerController::class, 'forceDestroy'])->name('customers.force-destroy');
    Route::resource('customers', CustomerController::class);
    
    // Items management
    Route::resource('items', ItemController::class);
    
    // Orders management
    Route::post('/orders/{order}/close', [OrderController::class, 'close'])->name('orders.close');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/update-total', [OrderController::class, 'updateTotal'])->name('orders.update-total');
    Route::resource('orders', OrderController::class);
    
    // Order lines
    Route::post('/orders/{order}/lines', [OrderLineController::class, 'store'])->name('orders.lines.store');
    Route::delete('/orders/{order}/lines/{orderLine}', [OrderLineController::class, 'destroy'])->name('orders.lines.destroy');
    
    // Payments
    Route::post('/orders/{order}/payments', [PaymentController::class, 'store'])->name('orders.payments.store');
    Route::delete('/orders/{order}/payments/{payment}', [PaymentController::class, 'destroy'])->name('orders.payments.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
