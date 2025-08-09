<?php

namespace App\Livewire\Report;

use Livewire\Component;
use App\Models\Movement;
use App\Models\Product;
use App\Models\Warehouse;
use Barryvdh\DomPDF\Facade\Pdf;

class HistoricalMovements extends Component
{
    public $productId;
    public $warehouseId;
    public $movementType;
    public $startDate;
    public $endDate;

    public $products;
    public $warehouses;

    public function mount()
    {
        $this->products = Product::all();
        $this->warehouses = Warehouse::all();
    }

    public function generatePdf()
    {
        $movements = $this->getMovementData();

        $pdf = Pdf::loadView('pdf.historical-movements', [
            'movements' => $movements,
            'filters' => [
                'product' => $this->productId ? Product::find($this->productId)->name : 'All',
                'warehouse' => $this->warehouseId ? Warehouse::find($this->warehouseId)->name : 'All',
                'type' => $this->movementType ? ucfirst($this->movementType) : 'All',
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
            ]
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'historical_movements_report.pdf');
    }

    public function getMovementData()
    {
        $query = Movement::with('product', 'warehouse');

        if ($this->productId) {
            $query->where('product_id', $this->productId);
        }

        if ($this->warehouseId) {
            $query->where('warehouse_id', $this->warehouseId);
        }

        if ($this->movementType) {
            $query->where('type', $this->movementType);
        }

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.report.historical-movements', [
            'movements' => $this->getMovementData(),
        ])->layout('layouts.app');
    }
}