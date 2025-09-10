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
    Route::get('/products/lista', App\Livewire\Product\Lista::class)->name('products.lista');
    Route::get('/products/modalproduct', App\Livewire\Product\ModalProduct::class)->name('products.modalproduct');
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
    
    //Para la lista de productos
    Route::get('/listado', App\Livewire\Petition\Listado::class)->name('petitions.listado');
    Route::get('/modallistado', App\Livewire\Petition\ModalListado::class)->name('petitions.modalListado');
    
    Route::get('/orders', App\Livewire\Order\Index::class)->name('orders.index');
    Route::get('/orders/create', App\Livewire\Order\Create::class)->name('orders.create');
    Route::get('/orders/{order}/edit', App\Livewire\Order\Edit::class)->name('orders.edit');
    Route::get('/orders/{order}', App\Livewire\Order\Show::class)->name('orders.show');
    Route::get('/reports/inventory-by-warehouse', App\Livewire\Report\InventoryByWarehouse::class)->name('reports.inventory-by-warehouse');
    Route::get('/reports/historical-movements', App\Livewire\Report\HistoricalMovements::class)->name('reports.historical-movements');

    Route::get('/prices', App\Livewire\Price\Index::class)->name('prices.index');
    Route::get('/prices/create', App\Livewire\Price\Create::class)->name('prices.create');
    Route::get('/prices/{price}/edit', App\Livewire\Price\Edit::class)->name('prices.edit');

    Route::get('/users', App\Livewire\UserManagement::class)->name('users.index');
    Route::get('/classifications', App\Livewire\Classification\Table::class)->name('classifications.table');
    
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');