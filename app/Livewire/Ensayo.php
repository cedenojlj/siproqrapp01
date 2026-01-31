<?php

namespace App\Livewire;
use App\Models\Classification;
use App\Models\Customer;
use App\Models\Petition;
use App\Models\PetitionClassification;
use App\Models\PetitionProduct;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductWarehouse;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Livewire\Component;

class Ensayo extends Component
{
      
    public $items = [];    
    public $totalAmount = 0;
    public $allWarehouses;
    public $warehouseId;

        protected $rules = [        
            'warehouseId' => 'required|exists:warehouses,id',
            'items.*' => 'nullable|integer|min:0',        
        ];

    public function mount()
    {
        
        $this->allWarehouses = Warehouse::all();
        $this->warehouseId = $this->allWarehouses->first()->id ?? null;

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

               /*  $products = Product::where('classification_id', $classificationId)
                    ->orderBy('created_at', 'asc')
                    ->get();

                foreach ($products as $product) {
                    if ($quantityToAssign <= 0) {
                        break;
                    }

                    
                } */


                // obtener el stock por clasificación y bodega
                    $stockByClassificationWarehouse = ProductWarehouse::join('products', 'product_warehouses.product_id', '=', 'products.id')
                        ->where('products.classification_id', $classificationId)
                        ->where('product_warehouses.warehouse_id', $this->warehouseId)
                        ->sum('product_warehouses.stock');

                    $stock = $stockByClassificationWarehouse;

                    // $stock = $product->getTotalStockAttribute();

                    if ($stock <= 0) {
                        continue;
                    }

                    $amountForThisProduct = min($quantityToAssign, $stock);
                    
                    PetitionClassification::create([
                        'petition_id' => $petition->id,
                        'classification_id' => $classificationId,                        
                        'quantity' => $amountForThisProduct,
                    ]);                    

                    $totalPetitionAmount += $amountForThisProduct;
                    // $quantityToAssign -= $amountForThisProduct;
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
                DB::raw('SUM(products.GN) as total_gn'),
            ])
            ->join('products', 'classifications.id', '=', 'products.classification_id')
            ->join('product_warehouses', 'products.id', '=', 'product_warehouses.product_id')
            ->where('product_warehouses.stock', '>', 0)
            ->where('product_warehouses.warehouse_id', $this->warehouseId)
            ->groupBy('classifications.id', 'classifications.code', 'classifications.description', 'classifications.size')
            ->orderBy('classifications.code', 'asc')
            ->get();
        
        
        return view('livewire.ensayo', [
            'classifications' => $classifications,
        ]);
    }
}
