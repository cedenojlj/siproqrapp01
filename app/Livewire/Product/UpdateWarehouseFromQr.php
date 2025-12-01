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
    public $cantidad = 0;
    public $warehousesAnterior;


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
            $this->warehousesAnterior = $productWarehouse->warehouse_id;
        }

        $this->dispatch('alert', type: 'success', message: 'Producto cargado. Por favor, asigne un almacén.');
    }

    public function updateWarehouse()
    {
        $this->validate([
            'selectedWarehouseId' => 'required',
            'cantidad' => 'required|integer|min:0',
            'warehousesAnterior' => 'required',
        ]);

        if ($this->cantidad == 0) {
            $this->dispatch('alert', type: 'error', message: 'La cantidad debe ser mayor a cero.');
            return;            
        }

        if (!$this->productFound) {
            $this->dispatch('alert', type: 'error', message: 'Primero debe escanear un producto.');
            return;
        }

        //stock actual segun producto y warehouse actual
        $productWarehouse = ProductWarehouse::where('product_id', $this->productData['id'])
            ->where('warehouse_id', $this->warehousesAnterior)
            ->first();

        /* if ($this->cantidad < 0) {
            $this->cantidad = 0;
        } */
        if ($this->cantidad > $productWarehouse->stock) {
            $this->cantidad = $productWarehouse->stock;
        }

        if ($this->cantidad == $productWarehouse->stock) {
            ProductWarehouse::updateOrCreate(
                ['product_id' => $this->productData['id'],'warehouse_id' => $this->warehousesAnterior],
                ['warehouse_id' => $this->selectedWarehouseId]
            );
        }

        if ($this->cantidad < $productWarehouse->stock) {

            $cantidadRestante = (int) $productWarehouse->stock - $this->cantidad;
            //dame el warehouse_id segun el product_id
            
            ProductWarehouse::updateOrCreate(
                ['product_id' => $this->productData['id'],'warehouse_id' => $this->warehousesAnterior],                
                ['stock' =>  $cantidadRestante]
            );            
            

            //crear stock en el warehouse destino con la cantidad restante product_id y warehouse_id y selectedWarehouseId
            ProductWarehouse::updateOrCreate(
                ['product_id' => $this->productData['id'],'warehouse_id' => $this->selectedWarehouseId],
                ['stock' => $this->cantidad]
            );   
            
        }       

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

    public function updatedCantidad($value)
    {
        //validar que la cantidad no sea negativa
       /*  if ($value < 0) {
            $this->cantidad = 0;
        } */

        //stock actual segun producto y warehouse
        $productWarehouse = ProductWarehouse::where('product_id', $this->productData['id'])
            ->where('warehouse_id', $this->warehousesAnterior)
            ->first();
        //validar que productWarehouse no sea null
        if (!$productWarehouse) {
           $this->cantidad = 0;
           return;
        }

        if ($value > $productWarehouse->stock) {
            $this->cantidad = (int) $productWarehouse->stock; //transformar a integer
        }
    }

    public function render()
    {
        return view('livewire.product.update-warehouse-from-qr');
    }
}
