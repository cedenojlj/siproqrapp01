<?php

namespace App\Livewire;
use App\Models\Classification;
use App\Models\Customer;
use App\Models\Petition;
use App\Models\PetitionProduct;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductWarehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Livewire\Component;

class Ensayo extends Component
{
   
   public $customerId;
    public $items = [];
    public $allCustomers;
    public $totalAmount = 0;

    protected $rules = [
        'customerId' => 'required|exists:customers,id',
    ];

    public function mount()
    {
        $this->allCustomers = Customer::all();
    }

    public function save()
    {
        $this->validate();

        if (empty($this->items)) {
            session()->flash('error', 'Debe solicitar al menos un producto.');
            return;
        }

        DB::transaction(function () {
            $petition = Petition::create([
                'customer_id' => $this->customerId,
                'user_id' => Auth::id(),
                'status' => 'Pendiente',
                'total' => 0, // Se calculará después
            ]);

            $totalPetitionAmount = 0;

            foreach ($this->items as $classificationId => $quantityRequested) {
                if ($quantityRequested <= 0) {
                    continue;
                }

                $quantityToAssign = $quantityRequested;

                $products = Product::where('classification_id', $classificationId)
                    ->orderBy('created_at', 'asc')
                    ->get();

                foreach ($products as $product) {
                    if ($quantityToAssign <= 0) {
                        break;
                    }

                    $stock = $product->getTotalStockAttribute();

                    if ($stock <= 0) {
                        continue;
                    }

                    $amountForThisProduct = min($quantityToAssign, $stock);

                    // Calcular precio
                    $priceRecord = Price::where('product_id', $product->id)
                        ->where('customer_id', $this->customerId)
                        ->first();

                    $price = 0;
                    if ($priceRecord) {
                        if ($product->classification->unit_type === 'Peso') {
                            $price = round($priceRecord->price_weight * $product->GN, 2);
                        } else {
                            $price = round($priceRecord->price_quantity, 2);
                        }
                    }
                    
                    $subtotal = $price * $amountForThisProduct;

                    PetitionProduct::create([
                        'petition_id' => $petition->id,
                        'product_id' => $product->id,
                        'quantity' => $amountForThisProduct,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ]);

                    $totalPetitionAmount += $subtotal;
                    $quantityToAssign -= $amountForThisProduct;
                }
            }

            // Actualizar el total de la petición
            $petition->update(['total' => $totalPetitionAmount]);
        });

        session()->flash('message', 'Pedido creado exitosamente.');
        return redirect()->route('petitions.index');
    }
   
   
   
   
    public function render()
    {
        
        $classifications = Classification::query()
            ->select([
                'classifications.id',
                'classifications.code',
                'classifications.description',
                'classifications.size',
                DB::raw('SUM(product_warehouses.stock) as total_stock'),
            ])
            ->join('products', 'classifications.id', '=', 'products.classification_id')
            ->join('product_warehouses', 'products.id', '=', 'product_warehouses.product_id')
            ->where('product_warehouses.stock', '>', 0)
            ->groupBy('classifications.id', 'classifications.code', 'classifications.description', 'classifications.size')
            ->get();
        
        
        return view('livewire.ensayo', [
            'classifications' => $classifications,
        ]);
    }
}
