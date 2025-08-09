<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', App\Livewire\Dashboard\Index::class)->name('dashboard.index');
    Route::get('/products', App\Livewire\Product\Index::class)->name('products.index');
    Route::get('/products/create', App\Livewire\Product\Create::class)->name('products.create');
    Route::get('/products/{product}/edit', App\Livewire\Product\Edit::class)->name('products.edit');
    Route::get('/products/generate-qrcodes', App\Livewire\Product\GenerateQrCodes::class)->name('products.generate-qrcodes');
    Route::get('/customers', App\Livewire\Customer\Index::class)->name('customers.index');
    Route::get('/customers/create', App\Livewire\Customer\Create::class)->name('customers.create');
    Route::get('/customers/{customer}/edit', App\Livewire\Customer\Edit::class)->name('customers.edit');
    Route::get('/warehouses', App\Livewire\Warehouse\Index::class)->name('warehouses.index');
    Route::get('/warehouses/create', App\Livewire\Warehouse\Create::class)->name('warehouses.create');
    Route::get('/warehouses/{warehouse}/edit', App\Livewire\Warehouse\Edit::class)->name('warehouses.edit');
    Route::get('/petitions', App\Livewire\Petition\Index::class)->name('petitions.index');
    Route::get('/petitions/create', App\Livewire\Petition\Create::class)->name('petitions.create');
    Route::get('/petitions/{petition}/edit', App\Livewire\Petition\Edit::class)->name('petitions.edit');
    Route::get('/petitions/{petition}', App\Livewire\Petition\Show::class)->name('petitions.show');
    Route::get('/orders', App\Livewire\Order\Index::class)->name('orders.index');
    Route::get('/orders/create', App\Livewire\Order\Create::class)->name('orders.create');
    Route::get('/orders/{order}/edit', App\Livewire\Order\Edit::class)->name('orders.edit');
    Route::get('/orders/{order}', App\Livewire\Order\Show::class)->name('orders.show');
    Route::get('/reports/inventory-by-warehouse', App\Livewire\Report\InventoryByWarehouse::class)->name('reports.inventory-by-warehouse');
    Route::get('/reports/historical-movements', App\Livewire\Report\HistoricalMovements::class)->name('reports.historical-movements');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');