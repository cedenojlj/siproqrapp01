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
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;


class Create extends Component
{
    public $customer_id;
    public $warehouse_id;
    public $order_type; // 'entry' or 'exit'
    public $products = [];
    public $scannedProductSku;
    public $totalAmount = 0;
    public $indice;

    protected $rules = [
        'customer_id' => 'required|exists:customers,id',
        'warehouse_id' => 'required|exists:warehouses,id',
        'order_type' => 'required|in:Entrada,Salida',
        'products.*.product_id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|numeric|min:1',
        'products.*.price' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        // $this->products[] = ['product_id' => '', 'quantity' => 1, 'price' => 0];
    }

    public function addProduct()
    {
        $this->products[] = ['product_id' => '', 'quantity' => 1, 'price' => 0];
    }

    public function removeProduct($index)
    {
        unset($this->products[$index]);
        $this->products = array_values($this->products);

        $this->totalAmount = $this->calculateTotalAmount();
    }

    public function updatedProducts($value, $key)
    {
        //$this->resetValidation();

        $parts = explode('.', $key);
        $index = $parts[0];
        $field = $parts[1];

        if ($field === 'product_id' && !empty($value)) {

            $this->products[$index]['product_id'] = $value;
            if (!isset($this->products[$index]['quantity'])) {
                $this->products[$index]['quantity'] = 1;
            }
            $this->calculateProductPrice($index);
        }

        if ($field === 'quantity' && !empty($value)) {
            $this->products[$index]['quantity'] = $value;
            $this->validarStock($index, $value);
        }

        if (!empty($value)) {

            $this->totalAmount = $this->calculateTotalAmount();
        }
    }

    public function updatedCustomerId()
    {
        foreach ($this->products as $index => $productData) {
            if (!empty($productData['product_id'])) {
                $this->calculateProductPrice($index);
            }
        }

        $this->totalAmount = $this->calculateTotalAmount();
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
            if ($classification->unit_type === 'Peso') {
                $this->products[$index]['price'] = $priceRecord->price_weight;
            } else {
                $this->products[$index]['price'] = $priceRecord->price_quantity;
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

        //funcion para buscar el stock del producto
        $stock = ProductWarehouse::where('product_id', $product->id)
            ->where('warehouse_id', $this->warehouse_id)
            ->first();

        if ($this->order_type === 'Salida') {
            if ($stock) {
                // Verificar si hay suficiente stock
                if ($stock->stock < 1) {
                    session()->flash('qr_error', 'No hay suficiente stock para el producto escaneado.');
                    return;
                }
            } else {
                session()->flash('qr_error', 'Producto no encontrado en el almacén.');
                return;
            }
        }



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

            // $this->eliminarProductosVacios();
            $this->totalAmount = $this->calculateTotalAmount();


            $this->scannedProductSku = '';
            session()->flash('qr_message', 'Product added from QR code!');
            $this->dispatch('qr-scanned-success');
        } else {
            session()->flash('qr_error', 'Product not found for scanned QR code.');
        }
    }

    public function saveOrder()
    {
        $this->validate();

        DB::transaction(function () {
            $order = Order::create([
                'customer_id' => $this->customer_id,
                'warehouse_id' => $this->warehouse_id,
                'order_type' => $this->order_type,
                'total' => $this->totalAmount,
                'status' => 'Aprobada', // Default status for simplicity
                'user_id' => Auth::id(),
            ]);

            foreach ($this->products as $productData) {
                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'price' => $productData['price'],
                    'subtotal' => $productData['quantity'] * $productData['price'],
                ]);

                // Update product stock in warehouse
                $productWarehouse = ProductWarehouse::firstOrNew([
                    'product_id' => $productData['product_id'],
                    'warehouse_id' => $this->warehouse_id,
                ]);

                if ($this->order_type === 'Entrada') {
                    $productWarehouse->stock += $productData['quantity'];
                } else { // Salida
                    $productWarehouse->stock -= $productData['quantity'];
                }
                $productWarehouse->save();

                // Create movement record
                Movement::create([
                    'product_id' => $productData['product_id'],
                    'warehouse_id' => $this->warehouse_id,
                    'quantity' => $productData['quantity'],
                    'type' => $this->order_type, // 'entry' or 'exit'
                    'order_id' => $order->id,
                    'date' => date('Y-m-d H:i:s'),
                ]);
            }
        });

        session()->flash('message', 'Order created successfully.');
        return redirect()->route('orders.index');
    }

    //funcion para calcular el monto total del pedido usando $this->products
    public function calculateTotalAmount()
    {
        $this->totalAmount = 0;
        foreach ($this->products as $product) {
            $this->totalAmount += ($product['price'] * $product['quantity']);
        }
        return $this->totalAmount;
    }

    function eliminarProductosVacios(): void
    {
        $this->products = array_filter($this->products, function ($product) {
            return !empty($product['product_id']) && $product['price'] > 0;
        });
    }

    //funcion para validar stock
    public function validarStock($index, $value): void
    {
        $productid = $this->products[$index]['product_id'];
        $productWarehouse = ProductWarehouse::where('product_id', $productid)
            ->where('warehouse_id', $this->warehouse_id)
            ->first();

        if ($this->order_type === 'Salida' && $productWarehouse && $value > $productWarehouse->stock) {
            $this->products[$index]['quantity'] = $productWarehouse->stock;
            //$this->addError('products.'.$index.'.quantity', 'No hay suficiente stock para el producto seleccionado.');
        }
    }

    //funcion para abrir el escáner
    public function abrirScanQrCode(): void
    {
        $this->dispatch('open-qr-scanner');
    }

    //FUNCION LEER SKU
    #[On('enviar-sku')]
    public function leerSku($sku): void
    {
        $this->scannedProductSku = $sku;
        $this->dispatch('close-qr-scanner');
    }

    public function render()
    {
        $customers = Customer::all();
        $warehouses = Warehouse::all();
        $allProducts = Product::all();

        // productos con stock mayor que 0
        $availableProducts = $allProducts->filter(function ($product) {
            return $product->getTotalStockAttribute() > 0;
        });

        return view('livewire.order.create', [
            'customers' => $customers,
            'warehouses' => $warehouses,
            'allProducts' => $allProducts,
            'availableProducts' => $availableProducts,
        ]);
    }
}
