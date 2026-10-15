<?php

use App\Http\Controllers\AddHubController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\InventoryInfoController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UsersHubController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Business Portal Routes - Core Modules
Route::middleware(['auth'])->group(function () {
    // 1. DASHBOARD
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Users Hub (Customers & Suppliers Unified Directory)
    Route::get('/users', [UsersHubController::class, 'index'])->name('users.index');

    // 2. ADD SECTION (Unified Hub + Individual Entry for Products, Suppliers, Customers)
    Route::get('/add', [AddHubController::class, 'index'])->name('add.index');

    // 3. SELL SECTION (POS & Sales)
    Route::get('/sell', [SaleController::class, 'create'])->name('sell.pos'); // Fast direct sell alias
    Route::resource('sales', SaleController::class);
    Route::get('sales/{sale}/print', [SaleController::class, 'print'])->name('sales.print');

    // 4. INFO SECTION (Inventory & Product Buying / Selling Price Total Intelligence)
    Route::get('/info', [InventoryInfoController::class, 'index'])->name('info.index');

    // 5. FINANCIAL REPORT SECTION (Daily, Monthly, Yearly Analysis)
    Route::get('/financial-reports', [FinancialReportController::class, 'index'])->name('reports.financial');
    Route::get('/reports', [FinancialReportController::class, 'index'])->name('reports.index');

    // Underlying Entities CRUD (Products, Suppliers, Customers, Categories)
    Route::resource('products', ProductController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('purchases', PurchaseController::class);

    // Payments & Credits (accessible via Sell & Finance views)
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/customer/create', [PaymentController::class, 'createCustomerPayment'])->name('payments.customer.create');
    Route::post('payments/customer', [PaymentController::class, 'storeCustomerPayment'])->name('payments.customer.store');
    Route::get('payments/supplier/create', [PaymentController::class, 'createSupplierPayment'])->name('payments.supplier.create');
    Route::post('payments/supplier', [PaymentController::class, 'storeSupplierPayment'])->name('payments.supplier.store');

    // Stock Movement Ledger & Adjustments
    Route::get('stock-transactions', [StockTransactionController::class, 'index'])->name('stock-transactions.index');
    Route::get('stock-adjustments/create', [StockTransactionController::class, 'createAdjustment'])->name('stock-adjustments.create');
    Route::post('stock-adjustments', [StockTransactionController::class, 'storeAdjustment'])->name('stock-adjustments.store');

    // Audit Trail
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});
