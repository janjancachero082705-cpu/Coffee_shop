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
use App\Http\Controllers\StoreInventoryController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ExpenseController;

// Root Redirect
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Custom Login Routes (Manual)
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Protected Routes (Kailangan naka-login)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Operations
    Route::resource('stores', StoreController::class);
    Route::resource('products', ProductController::class);
    
    // Deliveries
    Route::resource('deliveries', DeliveryController::class);
    Route::get('deliveries/store/{store}', [DeliveryController::class, 'showStore'])->name('deliveries.store-profile');
    
    // Inventory
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    
    // Consignment & Sales
    Route::get('sales-reports', [SalesReportController::class, 'index'])->name('sales-reports.index');
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('store-inventory', [StoreInventoryController::class, 'index'])->name('store-inventory.index');
    
    // Business Modules (Placeholders)
    Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('production', [ProductionController::class, 'index'])->name('production.index');
    Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses.index');
});