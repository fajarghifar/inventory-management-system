<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::view('profile', 'profile.index')->name('profile.index');

    Route::view('customers', 'customers.index')->name('customers.index');
    Route::view('suppliers', 'suppliers.index')->name('suppliers.index');
    Route::view('units', 'units.index')->name('units.index');
    Route::view('categories', 'categories.index')->name('categories.index');
    Route::view('products', 'products.index')->name('products.index');

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
});

require __DIR__.'/auth.php';
