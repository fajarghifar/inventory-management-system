<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceReportController;

Route::middleware(['auth', 'verified'])->group(function () {
    // =========================================================================
    // Dashboard & Profile (All Authenticated Users)
    // =========================================================================
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::view('profile', 'profile.index')->name('profile.index');

    // =========================================================================
    // Products & Stock Availability (All Users can view stock availability)
    // =========================================================================
    Route::prefix('master')->group(function () {
        Route::view('products', 'products.index')->name('products.index');
        Route::view('low-stock', 'products.low-in-stock-only')->name('low-stock.index');
    });

    // =========================================================================
    // Sales & POS (Available to Staff and Admin)
    // =========================================================================
    Route::resource('sales', SalesController::class)->except(['edit', 'update']);
    Route::prefix('sales/{sale}')->name('sales.')->controller(SalesController::class)->group(function () {
        Route::get('print', 'print')->name('print');
        Route::get('receipt', 'receipt')->name('receipt');
        Route::patch('complete', 'complete')->name('complete');
        Route::patch('restore', 'restore')->name('restore');
    });

    // =========================================================================
    // Admin Only: Master Data Management
    // =========================================================================
    Route::middleware('role:admin')->group(function () {
        Route::prefix('master')->group(function () {
            Route::view('customers', 'customers.index')->name('customers.index');
            Route::view('suppliers', 'suppliers.index')->name('suppliers.index');
            Route::view('categories', 'categories.index')->name('categories.index');
            Route::view('units', 'units.index')->name('units.index');
        });

        // Stock Adjustments & Movements
        Route::prefix('stock')->name('stock.')->group(function () {
            Route::view('adjustments', 'stock.adjustments')->name('adjustments');
            Route::view('movements', 'stock.movements')->name('movements');
        });

        // Purchases
        Route::resource('purchases', PurchaseController::class);
        Route::prefix('purchases/{purchase}')->name('purchases.')->controller(PurchaseController::class)->group(function () {
            Route::patch('ordered', 'markOrdered')->name('mark-ordered');
            Route::patch('received', 'markReceived')->name('mark-received');
            Route::patch('paid', 'markPaid')->name('mark-paid');
            Route::patch('cancel', 'cancel')->name('cancel');
            Route::patch('restore-draft', 'restoreToDraft')->name('restore-draft');
        });

        // Finance
        Route::prefix('finance')->name('finance.')->group(function () {
            Route::view('categories', 'finance-categories.index')->name('categories.index');
            Route::view('transactions', 'finance-transactions.index')->name('transactions.index');
            Route::get('transactions/print/{printId}', [FinanceReportController::class, 'print'])->name('transactions.print');
        });

        // Reports & Business Intelligence
        Route::prefix('reports')->name('reports.')->controller(\App\Http\Controllers\ReportController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/stock-valuation', 'stockValuation')->name('stock-valuation');
            Route::get('/slow-moving', 'slowMoving')->name('slow-moving');
            Route::get('/sales', 'sales')->name('sales');
            Route::get('/profit-loss', 'profitLoss')->name('profit-loss');
        });

        // Settings & Users
        Route::view('users', 'users.index')->name('users.index');
        Route::view('settings', 'settings.index')->name('settings.index');
    });

    // =========================================================================
    // Internal APIs (AJAX)
    // =========================================================================
    Route::prefix('ajax')->name('ajax.')->group(function () {
        Route::get('products/catalog', [\App\Http\Controllers\Api\ProductController::class, 'catalog'])->name('products.catalog');
        Route::post('products', [\App\Http\Controllers\Api\ProductController::class, 'search'])->name('products.search');
        Route::post('suppliers', [\App\Http\Controllers\Api\SupplierController::class, 'search'])->name('suppliers.search');
        Route::post('customers', [\App\Http\Controllers\Api\CustomerController::class, 'search'])->name('customers.search');
        Route::post('customers/store', [\App\Http\Controllers\Api\CustomerController::class, 'store'])->name('customers.store');
        Route::post('categories', [\App\Http\Controllers\Api\CategoryController::class, 'search'])->name('categories.search');
        Route::post('units', [\App\Http\Controllers\Api\UnitController::class, 'search'])->name('units.search');
        Route::post('users', [\App\Http\Controllers\Api\UserController::class, 'search'])->name('users.search');
        Route::post('finance-categories', [\App\Http\Controllers\Api\FinanceCategoryController::class, 'search'])->name('finance-categories.search');
    });
});

Route::view('/offline', 'offline')->name('offline');

require __DIR__.'/auth.php';
