<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\DeliveryReceiptController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\ConsignmentPaymentController;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::resource('products', ProductController::class);

    // Stores
    Route::resource('stores', StoreController::class);

    // Delivery Receipts
    Route::resource('deliveries', DeliveryReceiptController::class);

    // Consignment
    Route::prefix('consignment')->name('consignment.')->group(function () {
        Route::get('/reports', [SalesReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/create', [SalesReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [SalesReportController::class, 'store'])->name('reports.store');
        Route::get('/reports/{report}', [SalesReportController::class, 'show'])->name('reports.show');

        Route::get('/payments', [ConsignmentPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/create', [ConsignmentPaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [ConsignmentPaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}', [ConsignmentPaymentController::class, 'show'])->name('payments.show');
    });
});