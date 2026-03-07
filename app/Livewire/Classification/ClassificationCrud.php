<?php

namespace App\Livewire\Classification;

use Livewire\Component;
use App\Models\Classification;
use Livewire\WithPagination;

class ClassificationCrud extends Component
{
    use WithPagination;

    public $search = '';
    public $classification_id, $code, $description, $size, $precio_unidad, $precio_peso, $unit_type, $name;
    public $isOpen = 0;
    public $isDeleteModalOpen = 0;
    public $classificationToDelete;

    public function render()
    {
        $classifications = Classification::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('code', 'like', '%' . $this->search . '%')
            ->orWhere('description', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.classification.classification-crud', [
            'classifications' => $classifications,
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->classification_id = null;
        $this->code = '';
        $this->description = '';
        $this->size = '';
        $this->precio_unidad = '';
        $this->precio_peso = '';
        $this->unit_type = '';
        $this->name = '';
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:classifications,code,' . $this->classification_id,
            'description' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:50',
            'precio_unidad' => 'nullable|numeric',
            'precio_peso' => 'nullable|numeric',
            'unit_type' => 'nullable|string|max:50',
        ]);

        Classification::updateOrCreate(['id' => $this->classification_id], [
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'size' => $this->size,
            'precio_unidad' => $this->precio_unidad,
            'precio_peso' => $this->precio_peso,
            'unit_type' => $this->unit_type,
        ]);

        session()->flash('message',
            $this->classification_id ? 'Classification Updated Successfully.' : 'Classification Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit(Classification $classification)
    {
        $this->classification_id = $classification->id;
        $this->name = $classification->name;
        $this->code = $classification->code;
        $this->description = $classification->description;
        $this->size = $classification->size;
        $this->precio_unidad = $classification->precio_unidad;
        $this->precio_peso = $classification->precio_peso;
        $this->unit_type = $classification->unit_type;

        $this->openModal();
    }

    public function openDeleteModal(Classification $classification)
    {
        $this->classificationToDelete = $classification;
        $this->isDeleteModalOpen = true;
    }

    public function closeDeleteModal()
    {
        $this->isDeleteModalOpen = false;
        $this->classificationToDelete = null;
    }


    public function delete()
    {
        if ($this->classificationToDelete) {
            $this->classificationToDelete->delete();
            session()->flash('message', 'Classification deleted successfully.');
            $this->closeDeleteModal();
        }
    }
}
