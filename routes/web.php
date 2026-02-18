<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceReportController;

Route::middleware(['auth', 'verified'])->group(function () {
    // =========================================================================
    // Dashboard & Profile
    // =========================================================================
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::view('profile', 'profile.index')->name('profile.index');

    // =========================================================================
    // Master Data
    // =========================================================================
    Route::prefix('master')->group(function () {
        Route::view('customers', 'customers.index')->name('customers.index');
        Route::view('suppliers', 'suppliers.index')->name('suppliers.index');
        Route::view('categories', 'categories.index')->name('categories.index');
        Route::view('units', 'units.index')->name('units.index');
        Route::view('products', 'products.index')->name('products.index');
    });

    // =========================================================================
    // Transactions
    // =========================================================================

    // Purchases
    Route::resource('purchases', PurchaseController::class);
    Route::prefix('purchases/{purchase}')->name('purchases.')->controller(PurchaseController::class)->group(function () {
        Route::patch('ordered', 'markOrdered')->name('mark-ordered');
        Route::patch('received', 'markReceived')->name('mark-received');
        Route::patch('paid', 'markPaid')->name('mark-paid');
        Route::patch('cancel', 'cancel')->name('cancel');
        Route::patch('restore-draft', 'restoreToDraft')->name('restore-draft');
    });

    // Sales
    Route::resource('sales', SalesController::class);
    Route::prefix('sales/{sale}')->name('sales.')->controller(SalesController::class)->group(function () {
        Route::get('print', 'print')->name('print');
        Route::patch('complete', 'complete')->name('complete');
        Route::patch('restore', 'restore')->name('restore');
    });

    // POS API (Bridge for Frontend)
    Route::prefix('pos-api')->controller(PosController::class)->group(function () {
        Route::get('products', 'searchProducts');
        Route::get('customers', 'searchCustomers');
        Route::post('customers', 'storeCustomer');
        Route::post('sales', 'storeSale');
    });

    // =========================================================================
    // Finance
    // =========================================================================
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::view('categories', 'finance-categories.index')->name('categories.index');
        Route::view('transactions', 'finance-transactions.index')->name('transactions.index');
        Route::get('transactions/print/{printId}', [FinanceReportController::class, 'print'])->name('transactions.print');
    });

    // =========================================================================
    // Settings & Users
    // =========================================================================
    Route::view('users', 'users.index')->name('users.index');
    Route::view('settings', 'settings.index')->name('settings.index');

    // =========================================================================
    // Internal APIs (AJAX)
    // =========================================================================
    Route::prefix('ajax')->name('ajax.')->group(function () {
        Route::post('products', [\App\Http\Controllers\Api\ProductController::class, 'search'])->name('products.search');
        Route::post('suppliers', [\App\Http\Controllers\Api\SupplierController::class, 'search'])->name('suppliers.search');
        Route::post('categories', [\App\Http\Controllers\Api\CategoryController::class, 'search'])->name('categories.search');
        Route::post('units', [\App\Http\Controllers\Api\UnitController::class, 'search'])->name('units.search');
    });
});

require __DIR__.'/auth.php';
