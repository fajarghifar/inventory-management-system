<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

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
});

require __DIR__.'/auth.php';
