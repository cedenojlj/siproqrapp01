<?php

namespace App\Livewire\Report;

use App\Models\Classification;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryByClassification extends Component
{
    public $warehouseId;
    public $code;
    public $size;
    public $description;    

    public function render()
    {
        $data = $this->getData();
        $warehouses = Warehouse::all();

        return view('livewire.report.inventory-by-classification', [
            'data' => $data,
            'warehouses' => $warehouses,
        ]);
    }

    public function exportPdf()
    {
        $data = $this->getData();
        $pdf = Pdf::loadView('pdf.inventory-by-classification', ['data' => $data]);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'inventory-by-classification.pdf');
    }

    private function getData()
    {
        return Classification::query()
            ->select([
                'classifications.code',
                'classifications.description',
                'classifications.size',
                'classifications.unit_type',                
                DB::raw('COUNT(products.sku) as sku_count'),
                DB::raw('SUM(product_warehouses.stock) as total_stock'),
                DB::raw('SUM(products.gn) as total_gn'),
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
            ->where('product_warehouses.stock', '>', 0)
            ->groupBy(
                'classifications.code',
                'classifications.description',
                'classifications.size',
                'classifications.unit_type'
            )
            ->get();
    }
}