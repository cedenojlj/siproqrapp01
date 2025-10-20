<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\ProductWarehouse;
use Illuminate\Support\Facades\Log;

class UpdateWarehouseFromQr extends Component
{
    public $productData = [];
    public $selectedWarehouseId;
    public $warehouses = [];
    public bool $productFound = false;
    public $mostrarBotonScanner = false;
    public $escaneoInicial = true;
    

    public function mount()
    {
        $this->warehouses = Warehouse::all();
    }

    public function processQrCode($jsonPayload)
    {
        $this->resetState();
        $data = json_decode($jsonPayload, true);

        if (is_null($data) || !isset($data['id'])) {
            $this->dispatch('alert', type: 'error', message: 'El código QR no es válido.');
            return;
        }

        $product = Product::find($data['id']);

        if (!$product) {
            $this->dispatch('alert', type: 'error', message: 'Producto no encontrado en la base de datos.');
            return;
        }

        $this->productData = $data;
        $this->productFound = true;
        
        // Cargar el almacén actual si existe
         $productWarehouse = ProductWarehouse::where('product_id', $product->id)->first();

        if ($productWarehouse) {
            $this->selectedWarehouseId = $productWarehouse->warehouse_id;
        }

        $this->dispatch('alert', type: 'success', message: 'Producto cargado. Por favor, asigne un almacén.');
    }

    public function updateWarehouse()
    {
        $this->validate([
            'selectedWarehouseId' => 'required',
        ]);

        if (!$this->productFound) {
            $this->dispatch('alert', type: 'error', message: 'Primero debe escanear un producto.');
            return;
        }

        ProductWarehouse::updateOrCreate(
            ['product_id' => $this->productData['id']],
            ['warehouse_id' => $this->selectedWarehouseId]
        );

        session()->flash('message', 'Almacén actualizado con éxito para el producto: ' . $this->productData['sku']);

        $this->mostrarBotonScanner = true;

        $this->resetState();
    }
    
    public function resetState()
    {
        $this->productData = [];
        $this->selectedWarehouseId = null;
        $this->productFound = false;
    }

    public function render()
    {
        return view('livewire.product.update-warehouse-from-qr');
    }
}