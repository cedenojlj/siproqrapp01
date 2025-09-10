<?php

namespace App\Livewire\Classification;

use App\Models\Classification;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public $search = '';
    public $preciosUnidad = [];
    public $preciosPeso = [];

    /**
     * Mount the component and initialize the data.
     */
    public function mount()
    {
        $this->loadClassifications();
    }

    /**
     * Load classifications and populate the price arrays.
     */
    public function loadClassifications()
    {
        $classifications = Classification::all();
        foreach ($classifications as $classification) {
            $this->preciosUnidad[$classification->id] = $classification->precio_unidad;
            $this->preciosPeso[$classification->id] = $classification->precio_peso;
        }
    }

    /**
     * Update the prices for a specific classification.
     *
     * @param int $classificationId
     */
    public function update($classificationId)
    {
        $classification = Classification::find($classificationId);

        if (!$classification) {
            session()->flash('error', 'Clasificación no encontrada.');
            return;
        }

        $this->validate([
            'preciosUnidad.'.$classificationId => 'required|numeric|min:0',
            'preciosPeso.'.$classificationId => 'required|numeric|min:0',
        ]);

        $classification->update([
            'precio_unidad' => $this->preciosUnidad[$classificationId],
            'precio_peso' => $this->preciosPeso[$classificationId],
        ]);

        session()->flash('message', 'Precios de la clasificación \''. $classification->description .'\' actualizados correctamente.');
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $classifications = Classification::where('code', 'like', '%' . $this->search . '%')
            ->orWhere('description', 'like', '%' . $this->search . '%')
            ->orWhere('size', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.classification.table', [
            'classifications' => $classifications,
        ]);
    }
}
