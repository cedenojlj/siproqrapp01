<?php

namespace App\Livewire\Report;

use Livewire\Component;
use App\Models\PaymentApplication;
use App\Models\Customer;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentHistory extends Component
{
    use WithPagination;

    public $search_order = '';
    public $customer_id = '';
    public $fechaInicio;
    public $fechaFin;

    public function render()
    {
        $query = PaymentApplication::with(['payment.customer', 'order'])
            ->whereHas('payment', function ($q) {
                $q->when($this->customer_id, function ($q) {
                    $q->where('customer_id', $this->customer_id);
                });
            })
            ->when($this->search_order, function ($q) {
                $q->where('order_id', $this->search_order);
            })
            ->when($this->fechaInicio && $this->fechaFin, function ($q) {
                $q->whereHas('payment', function ($q) {
                    $q->whereBetween(DB::raw('DATE(fecha_pago)'), [$this->fechaInicio, $this->fechaFin]);
                });
            });

        $totalAbonado = $query->sum('monto_aplicado');

        $paymentApplications = $query->latest('id')->paginate(10);

        $customers = Customer::orderBy('name')->get();

        return view('livewire.report.payment-history', [
            'paymentApplications' => $paymentApplications,
            'totalAbonado' => $totalAbonado,
            'customers' => $customers,
        ]);
    }

    public function exportPdf()
    {
        $query = PaymentApplication::with(['payment.customer', 'order'])
            ->whereHas('payment', function ($q) {
                $q->when($this->customer_id, function ($q) {
                    $q->where('customer_id', $this->customer_id);
                });
            })
            ->when($this->search_order, function ($q) {
                $q->where('order_id', $this->search_order);
            })
            ->when($this->fechaInicio && $this->fechaFin, function ($q) {
                $q->whereHas('payment', function ($q) {
                    $q->whereBetween(DB::raw('DATE(fecha_pago)'), [$this->fechaInicio, $this->fechaFin]);
                });
            });

        $totalAbonado = $query->sum('monto_aplicado');
        $paymentApplications = $query->latest('id')->get();

        $pdf = Pdf::loadView('pdf.payment-history', [
            'paymentApplications' => $paymentApplications,
            'totalAbonado' => $totalAbonado
        ])->setPaper('letter', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'historial-abonos.pdf');
    }
}