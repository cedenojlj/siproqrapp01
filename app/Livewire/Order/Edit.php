<?php

namespace App\Livewire\Order;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Warehouse;
use App\Models\ProductWarehouse;
use App\Models\Movement;
use App\Models\Price;
use Illuminate\Support\Facades\DB;

class Edit extends Component
{
    public $order;
    public $customer_id;
    public $warehouse_id;
    public $order_type; // 'entry' or 'exit'
    public $products = [];
    public $scannedProductSku;

    protected $rules = [
        'customer_id' => 'required|exists:customers,id',
        'warehouse_id' => 'required|exists:warehouses,id',
        'order_type' => 'required|in:entry,exit',
        'products.*.product_id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|numeric|min:1',
        'products.*.price' => 'required|numeric|min:0',
    ];

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->customer_id = $order->customer_id;
        $this->warehouse_id = $order->warehouse_id;
        $this->order_type = $order->order_type;
        $this->products = $order->orderProducts->map(function ($item) {
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

    public function addProduct()
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
    }

    public function updateOrder()
    {
        $this->validate();

        DB::transaction(function () {
            // Revert previous stock changes and movements
            foreach ($this->order->orderProducts as $oldProduct) {
                $productWarehouse = ProductWarehouse::firstOrNew([
                    'product_id' => $oldProduct->product_id,
                    'warehouse_id' => $this->order->warehouse_id,
                ]);

                if ($this->order->order_type === 'entry') {
                    $productWarehouse->stock -= $oldProduct->quantity;
                } else { // exit
                    $productWarehouse->stock += $oldProduct->quantity;
                }
                $productWarehouse->save();

                // Delete old movement records associated with this order product
                Movement::where('order_id', $this->order->id)
                        ->where('product_id', $oldProduct->product_id)
                        ->where('quantity', $oldProduct->quantity)
                        ->where('type', $this->order->order_type)
                        ->delete();
            }

            $this->order->orderProducts()->delete(); // Remove old products

            $this->order->update([
                'customer_id' => $this->customer_id,
                'warehouse_id' => $this->warehouse_id,
                'order_type' => $this->order_type,
                'total_amount' => array_sum(array_column($this->products, 'price')),
            ]);

            foreach ($this->products as $productData) {
                OrderProduct::create([
                    'order_id' => $this->order->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'price' => $productData['price'],
                ]);

                // Update product stock in warehouse
                $productWarehouse = ProductWarehouse::firstOrNew([
                    'product_id' => $productData['product_id'],
                    'warehouse_id' => $this->warehouse_id,
                ]);

                if ($this->order_type === 'entry') {
                    $productWarehouse->stock += $productData['quantity'];
                } else { // exit
                    $productWarehouse->stock -= $productData['quantity'];
                }
                $productWarehouse->save();

                // Create movement record
                Movement::create([
                    'product_id' => $productData['product_id'],
                    'warehouse_id' => $this->warehouse_id,
                    'quantity' => $productData['quantity'],
                    'type' => $this->order_type, // 'entry' or 'exit'
                    'order_id' => $this->order->id,
                ]);
            }
        });

        session()->flash('message', 'Order updated successfully.');
        return redirect()->route('orders.index');
    }

    public function render()
    {
        $customers = Customer::all();
        $warehouses = Warehouse::all();
        $allProducts = Product::all();

        return view('livewire.order.edit', [
            'customers' => $customers,
            'warehouses' => $warehouses,
            'allProducts' => $allProducts,
        ]);
    }
}