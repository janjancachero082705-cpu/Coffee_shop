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

// ==========================================
// AUTH ROUTES (SECURED)
// ==========================================
// 1. Gi-usab ang URL gikan sa '/login' ngadto sa '/km-secure-login-2024'
// 2. Gibutangan og 'throttle:5,1' (5 attempts per minute ra para dili ma-brute force)
Route::get('/km-secure-login-2024', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/km-secure-login-2024', [App\Http\Controllers\Auth\LoginController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// ==========================================
// PROTECTED ROUTES (ADMIN ONLY)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Activity Logs
    Route::get('/activity-logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity.logs');
    Route::post('/activity-logs/{id}/read', [App\Http\Controllers\ActivityLogController::class, 'markAsRead'])->name('activity.logs.read');
    Route::post('/activity-logs/mark-all-read', [App\Http\Controllers\ActivityLogController::class, 'markAllAsRead'])->name('activity.logs.markAll');
    Route::put('/activity-logs/{id}', [App\Http\Controllers\ActivityLogController::class, 'update'])->name('activity.logs.update');
    Route::delete('/activity-logs/{id}', [App\Http\Controllers\ActivityLogController::class, 'destroy'])->name('activity.logs.destroy');
                
    // Management
    Route::resource('stores', StoreController::class);
    Route::resource('products', ProductController::class);
    Route::post('products/stock-in', [ProductController::class, 'stockIn'])->name('products.stock-in');
    Route::post('products/store-production', [ProductController::class, 'storeProduction'])->name('products.store-production');
    Route::resource('deliveries', DeliveryController::class);
    Route::get('deliveries/store/{store}', [DeliveryController::class, 'showStore'])->name('deliveries.store-profile');
    
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('inventory/stock-in', [InventoryController::class, 'stockIn'])->name('inventory.stock-in');
    
    // 🚨 FIXED: Gibalhin nako ni sa SULOD sa auth group!
    Route::get('/inventory/movements', [App\Http\Controllers\InventoryController::class, 'movements'])->name('inventory.movements');

    // Consignment & Sales
    Route::get('sales-reports', [SalesReportController::class, 'index'])->name('sales-reports.index');
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/live-data', [PaymentController::class, 'liveData'])->name('payments.live-data');

    // Business Modules (Purchases, Production, Expenses)
    Route::resource('purchases', PurchaseController::class);
    Route::resource('expenses', ExpenseController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
});