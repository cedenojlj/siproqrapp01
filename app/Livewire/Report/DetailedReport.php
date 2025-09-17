<?php

namespace App\Livewire\Report;

use Livewire\Component;
use App\Models\Movement;
use App\Models\Product;
use App\Models\Warehouse;
use Barryvdh\DomPDF\Facade\Pdf;

class DetailedReport extends Component
{
    public $productId;
    public $warehouseId;
    public $movementType;
    public $startDate;
    public $endDate;
    public $size;
    public $customerName;

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
        $totalSubtotal = $movements->sum('subtotal');

        $pdf = Pdf::loadView('pdf.detailed-report', [
            'movements' => $movements,
            'totalSubtotal' => $totalSubtotal,
            'filters' => [
                'product' => $this->productId ? Product::find($this->productId)->name : 'All',
                'warehouse' => $this->warehouseId ? Warehouse::find($this->warehouseId)->name : 'All',
                'type' => $this->movementType ? ucfirst($this->movementType) : 'All',
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
                'size' => $this->size,
                'customerName' => $this->customerName,
            ]
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'detailed_report.pdf');
    }

    public function getMovementData()
    {
        $query = Movement::query()
            ->leftJoin('order_products', function ($join) {
                $join->on('order_products.order_id', '=', 'movements.order_id')
                     ->on('order_products.product_id', '=', 'movements.product_id');
            })
            ->leftJoin('products', 'movements.product_id', '=', 'products.id')
            ->leftJoin('warehouses', 'movements.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('orders', 'movements.order_id', '=', 'orders.id')
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
            ->select(
                'movements.*',
                'products.name as product_name',
                'products.size as product_size',
                'products.GN as product_gn',
                'warehouses.name as warehouse_name',
                'customers.name as customer_name',
                'order_products.subtotal'
            );


        if ($this->size) {
            $query->where('products.size', 'like', '%' . $this->size . '%');
        }

        if ($this->customerName) {
            $query->where('customers.name', 'like', '%' . $this->customerName . '%');
        }

        if ($this->productId) {
            $query->where('movements.product_id', $this->productId);
        }

        if ($this->warehouseId) {
            $query->where('movements.warehouse_id', $this->warehouseId);
        }

        if ($this->movementType) {
            $query->where('movements.type', $this->movementType);
        }

        if ($this->startDate) {
            $query->whereDate('movements.created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('movements.created_at', '<=', $this->endDate);
        }

        return $query->orderBy('movements.created_at', 'desc')->get();
    }

    public function render()
    {
        $movements = $this->getMovementData();
        $totalSubtotal = $movements->sum('subtotal');

        return view('livewire.report.detailed-report', [
            'movements' => $movements,
            'totalSubtotal' => $totalSubtotal,
        ]);
    }
}