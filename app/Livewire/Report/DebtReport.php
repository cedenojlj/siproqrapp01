<?php

namespace App\Livewire\Report;

use Livewire\Component;
use App\Models\Order;
use App\Models\Customer;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DebtReport extends Component
{
    use WithPagination;

    public $search = '';
    public $customerId = '';
    public $startDate;
    public $endDate;
    public $orderType = '';
    public $status = '';
    public $paymentStatus = '';

    public function render()
    {
        $query = Order::with('customer', 'warehouse')
            ->where('payment_status', '!=', 'pagado')
            ->where('status', '!=', 'Rechazada')
            ->where('order_type', 'Salida')
            ->where(function ($query) {
                $query->where('status', 'like', '%' . $this->search . '%')
                    ->orWhere('order_type', 'like', '%' . $this->search . '%')
                    ->orWhere('payment_status', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('warehouse', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%');
                    });
            });

        if ($this->customerId) {
            $query->where('customer_id', $this->customerId);
        }

        if ($this->startDate && $this->endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$this->startDate, $this->endDate]);
        }

        if ($this->orderType) {
            $query->where('order_type', $this->orderType);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->paymentStatus) {
            $query->where('payment_status', $this->paymentStatus);
        }

        $orders = $query->orderBy('id', 'desc')->paginate(10);

        $totalPending = $orders->sum(function ($order) {
            return $order->total - $order->monto_pagado;
        });

        return view('livewire.report.debt-report', [
            'orders' => $orders,
            'customers' => Customer::all(),
            'totalPending' => $totalPending,
        ]);
    }

    public function generatePdf()
    {
        $query = Order::with('customer', 'warehouse')
            ->where('payment_status', '!=', 'pagado')
            ->where('status', '!=', 'Rechazada')
            ->where('order_type', 'Salida')
            ->where(function ($query) {
                $query->where('status', 'like', '%' . $this->search . '%')
                    ->orWhere('order_type', 'like', '%' . $this->search . '%')
                    ->orWhere('payment_status', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('warehouse', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%');
                    });
            });

        if ($this->customerId) {
            $query->where('customer_id', $this->customerId);
        }

        if ($this->startDate && $this->endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$this->startDate, $this->endDate]);
        }

        if ($this->orderType) {
            $query->where('order_type', $this->orderType);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->paymentStatus) {
            $query->where('payment_status', $this->paymentStatus);
        }

        $orders = $query->orderBy('id', 'desc')->get();

        $totalPending = $orders->sum(function ($order) {
            return $order->total - $order->monto_pagado;
        });

        $pdf = Pdf::loadView('livewire.report.debt-report-pdf', [
            'orders' => $orders,
            'totalPending' => $totalPending,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'debt-report.pdf');
    }
}