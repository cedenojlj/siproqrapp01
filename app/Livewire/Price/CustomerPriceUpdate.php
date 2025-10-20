<?php

namespace App\Livewire\Price;

use App\Models\Price;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerPriceUpdate extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $inputs = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = $this->getQuery()->paginate(15);

        // Pre-populate the inputs with current prices for the displayed page
        foreach ($data as $item) {
            $key = $item->customer_id . '.' . $item->classification_id;
            if (!isset($this->inputs[$item->customer_id][$item->classification_id])) {
                $this->inputs[$item->customer_id][$item->classification_id] = [
                    'price_quantity' => $item->price_quantity,
                    'price_weight' => $item->price_weight,
                ];
            }
        }

        return view('livewire.price.customer-price-update', [
            'data' => $data,
        ]);
    }

    private function getQuery()
    {
        // This query now groups by customer and classification
        $query = DB::table('prices')
            ->join('products', 'prices.product_id', '=', 'products.id')
            ->join('customers', 'prices.customer_id', '=', 'customers.id')
            ->join('classifications', 'products.classification_id', '=', 'classifications.id')
            ->select(
                'customers.id as customer_id',
                'customers.name as customer_name',
                'classifications.id as classification_id',
                'classifications.code as classification_code',
                'classifications.description as classification_description',
                'classifications.size as classification_size',
                // Select one of the prices from the group to display as current
                DB::raw('MIN(prices.price_quantity) as price_quantity'),
                DB::raw('MIN(prices.price_weight) as price_weight')
            )
            ->groupBy('customers.id', 'customers.name', 'classifications.id', 'classifications.code', 'classifications.description','classifications.size')
            ->orderBy('customers.id', 'asc')
            ->orderBy('classifications.id', 'asc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('classifications.code', 'like', '%' . $this->search . '%')
                    ->orWhere('classifications.description', 'like', '%' . $this->search . '%')
                    ->orWhere('classifications.size', 'like', '%' . $this->search . '%')
                    ->orWhere('customers.name', 'like', '%' . $this->search . '%');
            });
        }

        return $query;
    }

    public function save($customerId, $classificationId)
    {
        $validationRules = [
            'inputs.' . $customerId . '.' . $classificationId . '.price_quantity' => 'required|numeric|min:0',
            'inputs.' . $customerId . '.' . $classificationId . '.price_weight' => 'required|numeric|min:0',
        ];

        $this->validate($validationRules);

        try {
            $newPriceQuantity = $this->inputs[$customerId][$classificationId]['price_quantity'];
            $newPriceWeight = $this->inputs[$customerId][$classificationId]['price_weight'];

            // Mass update all prices for this customer and classification
            $updatedCount = Price::where('customer_id', $customerId)
                ->whereHas('product', function ($query) use ($classificationId) {
                    $query->where('classification_id', $classificationId);
                })
                ->update([
                    'price_quantity' => $newPriceQuantity,
                    'price_weight' => $newPriceWeight,
                ]);

            session()->flash('message', "Precios actualizados para la clasificación. Se afectaron {$updatedCount} productos.");

        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error al actualizar los precios: ' . $e->getMessage());
        }
    }
}
