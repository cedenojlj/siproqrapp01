<?php

namespace App\Livewire\Report;

use Livewire\Component;
use App\Models\Warehouse;
use App\Models\ProductWarehouse;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryByWarehouse extends Component
{
    public $selectedWarehouseId;
    public $warehouses;

    public function mount()
    {
        $this->warehouses = Warehouse::all();
    }

    public function generatePdf()
    {
        $inventory = $this->getInventoryData();

        $pdf = Pdf::loadView('pdf.inventory-by-warehouse', [
            'inventory' => $inventory,
            'warehouseName' => $this->selectedWarehouseId ? Warehouse::find($this->selectedWarehouseId)->name : 'All Warehouses',
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'inventory_report.pdf');
    }

    public function getInventoryData()
    {
        $query = ProductWarehouse::with('product', 'warehouse');

        if ($this->selectedWarehouseId) {
            $query->where('warehouse_id', $this->selectedWarehouseId);
        }

        return $query->get();
    }

    public function render()
    {
        return view('livewire.report.inventory-by-warehouse', [
            'inventory' => $this->getInventoryData(),
        ])->layout('layouts.app');
    }
}