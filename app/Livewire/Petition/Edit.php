<?php

namespace App\Livewire\Petition;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Petition;
use App\Models\PetitionProduct;
use App\Models\Price;

class Edit extends Component
{
    public $petition;
    public $customer_id;
    public $products = [];
    public $scannedProductSku;
    public $status;
    public $total;

    protected $rules = [
        // 'customer_id' => 'required|exists:customers,id',
        // 'products.*.product_id' => 'required|exists:products,id',
        // 'products.*.quantity' => 'required|numeric|min:1',
        // 'products.*.price' => 'required|numeric|min:0',
        'status' => 'required|in:Pendiente,Aprobada,Rechazada',
    ];

    public function mount(Petition $petition)
    {
        $this->petition = $petition;
        $this->total = $petition->total;
        $this->customer_id = $petition->customer_id;
        $this->status = $petition->status;
        $this->products = $petition->petitionProducts->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
            ];
        })->toArray();

        if (empty($this->products)) {
            $this->products[] = ['product_id' => '', 'quantity' => 1, 'price' => 0];
        }
    }

   /* public function addProduct()
    {
        $this->products[] = ['product_id' => '', 'quantity' => 1, 'price' => 0];
    }

    public function removeProduct($index)
    {
        unset($this->products[$index]);
        $this->products = array_values($this->products);
    }

    public function updatedProducts($value, $key)
    {
        $parts = explode('.', $key);
        $index = $parts[0];
        $field = $parts[1];

        if ($field === 'product_id' && !empty($value)) {
            $this->calculateProductPrice($index);
        }
    }

    public function updatedCustomerId()
    {
        foreach ($this->products as $index => $productData) {
            if (!empty($productData['product_id'])) {
                $this->calculateProductPrice($index);
            }
        }
    }

    public function calculateProductPrice($index)
    {
        $productId = $this->products[$index]['product_id'];
        $quantity = $this->products[$index]['quantity'];

        if (empty($productId) || empty($this->customer_id)) {
            $this->products[$index]['price'] = 0;
            return;
        }

        $product = Product::find($productId);
        if (!$product) {
            $this->products[$index]['price'] = 0;
            return;
        }

        $classification = $product->classification;
        $priceRecord = Price::where('product_id', $productId)
                            ->where('customer_id', $this->customer_id)
                            ->first();

        if ($classification && $priceRecord) {
            if ($classification->unit_type === 'weight') {
                $this->products[$index]['price'] = $priceRecord->price_weight * $quantity;
            } else {
                $this->products[$index]['price'] = $priceRecord->price_quantity * $quantity;
            }
        } else {
            $this->products[$index]['price'] = 0;
        }
    }

    public function scanQrCode()
    {
        if (empty($this->scannedProductSku)) {
            session()->flash('qr_error', 'Please scan a QR code or enter a SKU.');
            return;
        }

        $product = Product::where('sku', $this->scannedProductSku)->first();

        if ($product) {
            $found = false;
            foreach ($this->products as $index => $item) {
                if ($item['product_id'] == $product->id) {
                    $this->products[$index]['quantity']++;
                    $this->calculateProductPrice($index);
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $this->products[] = [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => 0, // Will be calculated by updatedProducts
                ];
                $this->calculateProductPrice(count($this->products) - 1);
            }

            $this->scannedProductSku = '';
            session()->flash('qr_message', 'Product added from QR code!');
            $this->dispatch('qr-scanned-success');
        } else {
            session()->flash('qr_error', 'Product not found for scanned QR code.');
        }
    } */

    public function updatePetition()
    {
        $this->validate();

        $this->petition->update([   

            'status' => $this->status,
        ]);

       // $this->petition->petitionProducts()->delete(); // Remove old products

        /* foreach ($this->products as $productData) {
            PetitionProduct::create([
                'petition_id' => $this->petition->id,
                'product_id' => $productData['product_id'],
                'quantity' => $productData['quantity'],
                'price' => $productData['price'],
            ]);
        } */

        session()->flash('message', 'Petition updated successfully.');
        return redirect()->route('petitions.index');
    }

    public function render()
    {
        $customers = Customer::all();
        $allProducts = Product::all();

        return view('livewire.petition.edit', [
            'customers' => $customers,
            'allProducts' => $allProducts,
        ]);
    }
}