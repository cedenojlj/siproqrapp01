<?php

namespace App\Livewire\Report;

use App\Models\Classification;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryByClassificationDetail extends Component
{
    public $warehouseId;
    public $code;
    public $size;
    public $description;
    public $sku;

    public function render()
    {
        $data = $this->getData();
        $warehouses = Warehouse::all();

        return view('livewire.report.inventory-by-classification-detail', [
            'data' => $data,
            'warehouses' => $warehouses,
        ]);
    }

    public function exportPdf()
    {
        $data = $this->getData();
        $pdf = Pdf::loadView('pdf.inventory-by-classification-detail', ['data' => $data]);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'inventory-by-classification-detail.pdf');
    }

    private function getData()
    {
        return Classification::query()
            ->select([
                'classifications.code',
                'classifications.description',
                'classifications.size',
                'classifications.unit_type',
                'products.sku',
                'product_warehouses.stock',
            ])
            ->join('products', 'classifications.id', '=', 'products.classification_id')
            ->join('product_warehouses', 'products.id', '=', 'product_warehouses.product_id')
            ->when($this->warehouseId, function ($query) {
                $query->where('product_warehouses.warehouse_id', $this->warehouseId);
            })
            ->when($this->code, function ($query) {
                $query->where('classifications.code', 'like', '%' . $this->code . '%');
            })
            ->when($this->size, function ($query) {
                $query->where('classifications.size', 'like', '%' . $this->size . '%');
            })
            ->when($this->description, function ($query) {
                $query->where('classifications.description', 'like', '%' . $this->description . '%');
            })
            ->when($this->sku, function ($query) {
                $query->where('products.sku', 'like', '%' . $this->sku . '%');
            })
            ->where('product_warehouses.stock', '>', 0)
            ->get();
    }
}