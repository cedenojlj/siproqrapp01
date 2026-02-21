<?php

namespace App\Livewire\Report;

use Livewire\Component;
use App\Models\Customer;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerDebtReport extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $customersQuery = Customer::with(['orders' => function ($query) {
            $query->where('payment_status', '!=', 'pagado')
                ->where('status', '!=', 'Rechazada')
                ->where('order_type', 'Salida');
        }])
        ->where(function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
        })
        ->select('customers.*')
        ->addSelect(DB::raw('(SELECT SUM(total - monto_pagado) FROM orders WHERE orders.customer_id = customers.id AND orders.payment_status != "pagado" AND orders.status != "Rechazada" AND orders.order_type = "Salida") as total_debt'))
        ->having('total_debt', '>', 0)
        ->orderBy('total_debt', 'desc');

        $customers = $customersQuery->paginate(10);
        
        $grandTotalDebt = $customersQuery->get()->sum('total_debt');


        return view('livewire.report.customer-debt-report', [
            'customers' => $customers,
            'grandTotalDebt' => $grandTotalDebt
        ]);
    }

    public function generatePdf()
    {
        $customersQuery = Customer::with(['orders' => function ($query) {
            $query->where('payment_status', '!=', 'pagado')
                ->where('status', '!=', 'Rechazada')
                ->where('order_type', 'Salida');
        }])
        ->where(function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
        })
        ->select('customers.*')
        ->addSelect(DB::raw('(SELECT SUM(total - monto_pagado) FROM orders WHERE orders.customer_id = customers.id AND orders.payment_status != "pagado" AND orders.status != "Rechazada" AND orders.order_type = "Salida") as total_debt'))
        ->having('total_debt', '>', 0)
        ->orderBy('total_debt', 'desc');

        $customers = $customersQuery->get();
        
        $grandTotalDebt = $customers->sum('total_debt');

        $pdf = Pdf::loadView('livewire.report.customer-debt-report-pdf', [
            'customers' => $customers,
            'grandTotalDebt' => $grandTotalDebt,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'customer-debt-report.pdf');
    }
}
