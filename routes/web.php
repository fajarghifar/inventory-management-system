<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceReportController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::view('profile', 'profile.index')->name('profile.index');

    Route::view('customers', 'customers.index')->name('customers.index');
    Route::view('suppliers', 'suppliers.index')->name('suppliers.index');
    Route::view('units', 'units.index')->name('units.index');
    Route::view('categories', 'categories.index')->name('categories.index');
    Route::view('products', 'products.index')->name('products.index');
    Route::view('finance/categories', 'finance-categories.index')->name('finance.categories.index');
    Route::get('finance/transactions/print/{printId}', [FinanceReportController::class, 'print'])->name('finance.transactions.print');
    Route::view('finance/transactions', 'finance-transactions.index')->name('finance.transactions.index');
    Route::view('users', 'users.index')->name('users.index');
    Route::view('settings', 'settings.index')->name('settings.index');

    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::get('/purchases/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::patch('/purchases/{purchase}/ordered', [PurchaseController::class, 'markOrdered'])->name('purchases.mark-ordered');
    Route::patch('/purchases/{purchase}/received', [PurchaseController::class, 'markReceived'])->name('purchases.mark-received');
    Route::patch('/purchases/{purchase}/paid', [PurchaseController::class, 'markPaid'])->name('purchases.mark-paid');
    Route::patch('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
    Route::patch('/purchases/{purchase}/restore-draft', [PurchaseController::class, 'restoreToDraft'])->name('purchases.restore-draft');
    Route::delete('/purchases/{purchase}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');

    Route::get('/sales/{sale}/print', [SalesController::class, 'print'])->name('sales.print');
    Route::patch('/sales/{sale}/complete', [SalesController::class, 'complete'])->name('sales.complete');
    Route::patch('/sales/{sale}/restore', [SalesController::class, 'restore'])->name('sales.restore');
    Route::prefix('pos-api')->group(function () {
        Route::get('/products', [PosController::class, 'searchProducts']);
        Route::get('/customers', [PosController::class, 'searchCustomers']);
        Route::post('/customers', [PosController::class, 'storeCustomer']);
        Route::post('/sales', [PosController::class, 'storeSale']);
    });

    // Internal API for TomSelect (Search) - Uses Web Session
    Route::prefix('ajax')->group(function () {
        Route::get('/products', [SearchController::class, 'searchProducts'])->name('ajax.products.search');
        Route::get('/suppliers', [SearchController::class, 'searchSuppliers'])->name('ajax.suppliers.search');
    });

    Route::resource('sales', SalesController::class);
});

require __DIR__.'/auth.php';
