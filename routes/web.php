<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Business Portal Routes
Route::middleware(['auth'])->group(function () {
    // Root & Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Products & Categories
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);

    // Suppliers & Purchases (Stock-In)
    Route::resource('suppliers', SupplierController::class);
    Route::resource('purchases', PurchaseController::class);

    // Customers & Sales (Stock-Out)
    Route::resource('customers', CustomerController::class);
    Route::resource('sales', SaleController::class);
    Route::get('sales/{sale}/print', [SaleController::class, 'print'])->name('sales.print');

    // Credit Management & Multi-Installment Payments
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/customer/create', [PaymentController::class, 'createCustomerPayment'])->name('payments.customer.create');
    Route::post('payments/customer', [PaymentController::class, 'storeCustomerPayment'])->name('payments.customer.store');
    Route::get('payments/supplier/create', [PaymentController::class, 'createSupplierPayment'])->name('payments.supplier.create');
    Route::post('payments/supplier', [PaymentController::class, 'storeSupplierPayment'])->name('payments.supplier.store');

    // Stock Movement Ledger & Adjustments
    Route::get('stock-transactions', [StockTransactionController::class, 'index'])->name('stock-transactions.index');
    Route::get('stock-adjustments/create', [StockTransactionController::class, 'createAdjustment'])->name('stock-adjustments.create');
    Route::post('stock-adjustments', [StockTransactionController::class, 'storeAdjustment'])->name('stock-adjustments.store');

    // Reports & Business Intelligence
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    // Audit Trail
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});
