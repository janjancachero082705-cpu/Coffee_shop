<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ExpenseController;

// Root Redirect
Route::get('/', function () { return redirect()->route('dashboard'); });
Route::get('/admin', function () { return redirect()->route('dashboard'); });

// Auth Routes (Custom Login)
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Management
    Route::resource('stores', StoreController::class);
    Route::resource('products', ProductController::class);
    Route::post('products/stock-in', [ProductController::class, 'stockIn'])->name('products.stock-in');
    Route::post('products/store-production', [ProductController::class, 'storeProduction'])->name('products.store-production');
    Route::resource('deliveries', DeliveryController::class);
    Route::get('deliveries/store/{store}', [DeliveryController::class, 'showStore'])->name('deliveries.store-profile');
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('inventory/stock-in', [InventoryController::class, 'stockIn'])->name('inventory.stock-in');

    // Consignment & Sales
    Route::get('sales-reports', [SalesReportController::class, 'index'])->name('sales-reports.index');
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/live-data', [PaymentController::class, 'liveData'])->name('payments.live-data');

    // Business Modules (Purchases, Production, Expenses)
    Route::resource('purchases', PurchaseController::class);
    
    Route::resource('expenses', ExpenseController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
});