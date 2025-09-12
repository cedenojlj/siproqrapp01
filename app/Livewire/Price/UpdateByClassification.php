<?php

namespace App\Livewire\Price;

use App\Models\Classification;
use App\Models\Customer;
use App\Models\Price;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class UpdateByClassification extends Component
{
    //use LivewireAlert;

    public $classifications = [];
    public $customers = [];

    public $selectedClassification;
    public $precio_unidad;
    public $precio_peso;
    public $selectedCustomers = [];

    protected $rules = [
        'selectedClassification' => 'required|exists:classifications,id',
        'precio_unidad' => 'required|numeric|min:0',
        'precio_peso' => 'required|numeric|min:0',
        'selectedCustomers' => 'required|array|min:1',
        'selectedCustomers.*' => 'exists:customers,id',
    ];

    protected $messages = [
        'selectedClassification.required' => 'Debe seleccionar una clasificación.',
        'precio_unidad.required' => 'El precio por unidad es obligatorio.',
        'precio_unidad.numeric' => 'El precio por unidad debe ser un número.',
        'precio_peso.required' => 'El precio por peso es obligatorio.',
        'precio_peso.numeric' => 'El precio por peso debe ser un número.',
        'selectedCustomers.required' => 'Debe seleccionar al menos un cliente.',
        'selectedCustomers.min' => 'Debe seleccionar al menos un cliente.',
    ];

    public function mount()
    {
        $this->classifications = Classification::all();
        $this->customers = Customer::all();
    }

    public function render()
    {
        return view('livewire.price.update-by-classification');
    }

    public function updatePrices()
    {
        $this->validate();

        try {
            DB::transaction(function () {

                //actualizar el precio_quantity y price_weight de la tabla prices, segun la classification_id y customer_id, la classification_id lo tiene el producto relacionado
                $prices = Price::whereIn('customer_id', $this->selectedCustomers)
                    ->whereHas('product', function ($query) {
                        $query->where('classification_id', $this->selectedClassification);
                    })
                    ->get();

                foreach ($prices as $price) {
                    $price->price_quantity = $this->precio_unidad;
                    $price->price_weight = $this->precio_peso;
                    $price->save();
                }

               /* $updatedCount = Product::whereIn('customer_id', $this->selectedCustomers)
                    ->where('classification_id', $this->selectedClassification)
                    ->update([
                        'price_quantity' => $this->precio_unidad,
                        'price_weight' => $this->precio_peso,
                    ]); */

                    

                session()->flash('success', "¡Precios actualizados!"); 
            });

            $this->resetForm();

        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error al actualizar los precios: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->reset(['selectedClassification', 'precio_unidad', 'precio_peso', 'selectedCustomers']);
    }
}