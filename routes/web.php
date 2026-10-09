<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\DeliveryReceiptController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\ConsignmentPaymentController;
use App\Http\Controllers\ReorderRequestController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\Portal\StoreAuthController;
use App\Http\Controllers\Portal\StoreRegisterController;
use App\Http\Controllers\Portal\PortalProfileController;
use App\Http\Controllers\StoreRegistrationController;
use App\Http\Controllers\Portal\PortalDashboardController;
use App\Http\Controllers\Portal\PortalOrderController;
use App\Http\Controllers\Portal\PortalDeliveryController;
use App\Http\Controllers\Portal\PortalSalesReportController;
use App\Http\Controllers\Portal\PortalPaymentController;

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

    // Finance Dashboard
    Route::get('/finance', [\App\Http\Controllers\FinanceController::class, 'dashboard'])->name('finance.dashboard');

        // Transactions (Activity Log)
    Route::get('/transactions', [\App\Http\Controllers\TransactionController::class, 'index'])->name('transactions.index');

    // Products
    Route::resource('products', ProductController::class);

    // Stores
    Route::resource('stores', StoreController::class);

    // Store Registrations
    Route::get('store-registrations', [StoreRegistrationController::class, 'index'])->name('store-registrations.index');
    Route::get('store-registrations/{store}', [StoreRegistrationController::class, 'show'])->name('store-registrations.show');
    Route::post('store-registrations/{store}/approve', [StoreRegistrationController::class, 'approve'])->name('store-registrations.approve');
    Route::post('store-registrations/{store}/reject', [StoreRegistrationController::class, 'reject'])->name('store-registrations.reject');

    // Delivery Receipts
    Route::resource('reorder-requests', \App\Http\Controllers\ReorderRequestController::class)->only(['index', 'show']);
    Route::post('reorder-requests/{reorderRequest}/approve', [\App\Http\Controllers\ReorderRequestController::class, 'approve'])->name('reorder-requests.approve');
    Route::post('reorder-requests/{reorderRequest}/reject', [\App\Http\Controllers\ReorderRequestController::class, 'reject'])->name('reorder-requests.reject');

    Route::resource('deliveries', DeliveryReceiptController::class);
    Route::post('deliveries/{id}/out-for-delivery', [\App\Http\Controllers\DeliveryReceiptController::class, 'markOutForDelivery'])->name('deliveries.out-for-delivery');

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

// ==================================================================
// STORE PORTAL
// ==================================================================

Route::prefix('portal')->name('portal.')->group(function () {

    // Guest routes
    Route::middleware('guest:store')->group(function () {
        Route::get('/login', [StoreAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [StoreAuthController::class, 'login'])->name('login.store');
        Route::get('/register', [StoreRegisterController::class, 'showForm'])->name('register');
        Route::post('/register', [StoreRegisterController::class, 'register'])->name('register.store');
        Route::get('/register/success', [StoreRegisterController::class, 'success'])->name('register.success');
    });

    // Authenticated store routes
    Route::middleware('auth:store')->group(function () {
        Route::post('/logout', [StoreAuthController::class, 'logout'])->name('logout');
        Route::get('/', [PortalDashboardController::class, 'index'])->name('dashboard');

        // Profile
        Route::get('/profile', [PortalProfileController::class, 'show'])->name('profile');
        Route::get('/profile/edit', [PortalProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [PortalProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile/logo', [PortalProfileController::class, 'removeLogo'])->name('profile.logo.remove');
        Route::get('/profile/password', [PortalProfileController::class, 'passwordForm'])->name('profile.password');
        Route::put('/profile/password', [PortalProfileController::class, 'updatePassword'])->name('profile.password.update');

        // Orders
        Route::get('/orders', [PortalOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [PortalOrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [PortalOrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{id}', [PortalOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{id}/cancel', [PortalOrderController::class, 'cancel'])->name('orders.cancel');

        // Deliveries
        Route::get('/deliveries', [PortalDeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('/deliveries/{id}', [PortalDeliveryController::class, 'show'])->name('deliveries.show');
        Route::post('/deliveries/{id}/confirm', [PortalDeliveryController::class, 'confirm'])->name('deliveries.confirm');

        // Sales Reports
        Route::get('/reports', [PortalSalesReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{id}', [PortalSalesReportController::class, 'show'])->name('reports.show');

        // Payments
        Route::get('/payments', [PortalPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/create', [PortalPaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [PortalPaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{id}', [PortalPaymentController::class, 'show'])->name('payments.show');

        // Notifications
        Route::get('/notifications/unread', [\App\Http\Controllers\Portal\NotificationController::class, 'unread'])->name('notifications.unread');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Portal\NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\Portal\NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    });
});

// ============ ADMIN NOTIFICATIONS ============
Route::middleware(['auth'])->group(function () {
    Route::get('notifications', [\App\Http\Controllers\AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/read-all', [\App\Http\Controllers\AdminNotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('notifications/{id}/read', [\App\Http\Controllers\AdminNotificationController::class, 'markRead'])->name('notifications.read');
});
// Nav badge polling endpoint
Route::middleware('auth')->get('/nav/pending-orders', function () {
    return response()->json([
        'pending'  => \App\Models\ReorderRequest::whereRaw('LOWER(status) = ?', ['pending'])->count(),
        'total'    => \App\Models\ReorderRequest::count(),
        'approved' => \App\Models\ReorderRequest::whereRaw('LOWER(status) = ?', ['approved'])->count(),
        'rejected' => \App\Models\ReorderRequest::whereRaw('LOWER(status) = ?', ['rejected'])->count(),
    ]);
})->name('nav.pending-orders');

// ═══════════ PAYMENT VERIFICATION ═══════════
Route::middleware('auth')->prefix('consignment/payments')->name('consignment.payments.')->group(function () {
    Route::post('{id}/verify', [\App\Http\Controllers\ConsignmentPaymentController::class, 'verify'])->name('verify');
    Route::post('{id}/reject', [\App\Http\Controllers\ConsignmentPaymentController::class, 'reject'])->name('reject');
});
