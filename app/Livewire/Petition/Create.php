<?php

namespace App\Livewire\Petition;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Petition;
use App\Models\PetitionProduct;
use App\Models\Price;
use App\Models\ProductWarehouse;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class Create extends Component
{
    public $customer_id;
    public $products = [];
    public $scannedProductSku;
    public $totalAmount = 0;
    public $indice;

    protected $rules = [
        'customer_id' => 'required|exists:customers,id',
        'products.*.product_id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|numeric|min:1',
        'products.*.price' => 'required|numeric|min:0',
    ];

    function abrirModal($indice)
    {

        $this->indice = $indice;
        $this->dispatch('abrirModalListado');
    }

    //funcion para actualizar el producto en la lista de productos
    #[On('colocarProducto')]
    function actualizarProducto($idproducto)
    {
        // Validar stock antes de actualizar el producto

        if (!$this->validateStock($idproducto)) {
            return; // Si no hay stock, salir de la función
        }

        if (isset($this->products[$this->indice])) {
            $this->products[$this->indice]['product_id'] = $idproducto;
            $this->calculateProductPrice($this->indice);
            $this->products[$this->indice]['quantity'] = $this->getMaxStock($idproducto);
            $this->totalAmount = $this->calculateTotalAmount();
        } else {
            session()->flash('error', 'Invalid product index.');
        }
    }

    //funcion para determinar el stock maximo del producto dado su id y unit_type Peso
    public function getMaxStock($idproducto)
    {
        $producto = Product::find($idproducto);

        if (!$producto) {
            session()->flash('qr_error', 'Producto no encontrado');
            return false;
        }

        if ($producto->classification->unit_type === 'Peso') {
            $cantidadmaxima = $producto->getTotalStockAttribute();
            return $cantidadmaxima;
        } else {
            return 1;
        };
    }


    public function mount()
    {
        $this->products[] = ['product_id' => '', 'quantity' => 1, 'price' => 0];
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
        // Split the key to get the index and field name       
        $parts = explode('.', $key);
        $index = $parts[0];
        $field = $parts[1];

        if ($field === 'product_id' && !empty($value)) {
            $this->products[$index]['product_id'] = $value;
            $this->products[$index]['quantity'] = $this->getMaxStock($value);
            //dd($index);

            $this->calculateProductPrice($index);
        }

        if ($field === 'quantity' && !empty($value)) {
            $this->products[$index]['quantity'] = $value;
            $this->validateMaxQuantity($index, $value); // Validate max quantity

        }

        if (!empty($value)) {
            // Recalculate the total amount whenever a product detail is updated
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

        //suma de stock por producto
        $stock = ProductWarehouse::where('product_id', $product->id)
            ->sum('stock');

        if ($stock < 1) {
            session()->flash('qr_error', 'No hay suficiente stock para el producto escaneado.');
            return;
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

            $this->scannedProductSku = '';
            session()->flash('qr_message', 'Product added from QR code!');
            $this->dispatch('qr-scanned-success');
        } else {
            session()->flash('qr_error', 'Product not found for scanned QR code.');
        }
    }

    public function savePetition()
    {
        $this->validate();

        $petition = Petition::create([
            'customer_id' => $this->customer_id,
            'total' => $this->totalAmount,
            'status' => 'Pendiente', // Default status
            'user_id' => Auth::id(), // Assuming you have Auth facade available
        ]);

        foreach ($this->products as $productData) {
            PetitionProduct::create([
                'petition_id' => $petition->id,
                'product_id' => $productData['product_id'],
                'quantity' => $productData['quantity'],
                'price' => $productData['price'],
                'subtotal' => $productData['price'] * $productData['quantity'],
            ]);
        }

        session()->flash('message', 'Petition created successfully.');
        return redirect()->route('petitions.index');
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

    //funcion para validar stock
    public function validateStock($idproducto)
    {

        $producto = Product::find($idproducto);

        if (!$producto) {
            session()->flash('qr_error', 'Producto no encontrado');
            return false;
        }

        if ($producto->getTotalStockAttribute() < 1) {
            session()->flash('qr_error', 'No hay suficiente stock para el producto');
            return false;
        }
        return true;
    }

    //funcion para validar maxima cantidad del producto
    public function validateMaxQuantity($index, $value): void
    {
        $producto = Product::find($this->products[$index]['product_id']);

        $cantidadmaxima = $producto->getTotalStockAttribute();
        if ($value > $cantidadmaxima) {
            $this->products[$index]['quantity'] = $cantidadmaxima;
            session()->flash('qr_error', 'Cantidad maxima alcanzada para el producto');
        }
    }

    public function render()
    {
        $customers = Customer::all();
        $allProducts = Product::all();

        // productos con stock mayor que 0
        $availableProducts = $allProducts->filter(function ($product) {
            return $product->getTotalStockAttribute() > 0;
        });

        return view('livewire.petition.create', [
            'customers' => $customers,
            'allProducts' => $allProducts,
            'availableProducts' => $availableProducts,
        ]);
    }
}
