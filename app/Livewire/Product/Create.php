<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use App\Models\Classification;
use App\Models\Warehouse;

class Create extends Component
{
    public $name;
    public $description;
    public $sku;
    public $price;
    public $stock;
    public $classification_id;
    public $warehouse_id;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'sku' => 'required|string|max:255|unique:products,sku',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'classification_id' => 'required|exists:classifications,id',
        'warehouse_id' => 'required|exists:warehouses,id',
    ];

    public function mount()
    {
        // Initialize with default values if needed
    }

    public function save()
    {
        $this->validate();

        Product::create([
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'price' => $this->price,
            'stock' => $this->stock,
            'classification_id' => $this->classification_id,
            'warehouse_id' => $this->warehouse_id,
        ]);

        session()->flash('message', 'Product created successfully.');

        $this->reset(); // Clear form fields after saving

        return redirect()->route('products.index');
    }

    public function render()
    {
        $classifications = Classification::all();
        $warehouses = Warehouse::all();

        return view('livewire.product.create', [
            'classifications' => $classifications,
            'warehouses' => $warehouses,
        ]);
    }

    public function fillFormFromQrCode($data)
    {
        $scannedData = json_decode($data, true);

        if ($scannedData) {
            $this->name = $scannedData['name'] ?? '';
            $this->description = $scannedData['description'] ?? '';
            $this->sku = $scannedData['sku'] ?? '';
            $this->price = $scannedData['price'] ?? 0;
            $this->stock = $scannedData['stock'] ?? 0;
            $this->classification_id = $scannedData['classification_id'] ?? '';
            $this->warehouse_id = $scannedData['warehouse_id'] ?? '';

            $this->dispatch('qr-scanned-success'); // Emit event for UI feedback
            session()->flash('qr_message', 'QR code scanned successfully!');
        } else {
            session()->flash('qr_error', 'Invalid QR code data.');
        }
    }
}