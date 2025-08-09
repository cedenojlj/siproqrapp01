<?php

namespace App\Livewire\Order;

use Livewire\Component;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class Show extends Component
{
    public $order;

    public function mount(Order $order)
    {
        $this->order = $order;
    }

    public function generatePdf()
    {
        $pdf = Pdf::loadView('pdf.order-details', ['order' => $this->order]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'order_' . $this->order->id . '.pdf');
    }

    public function render()
    {
        return view('livewire.order.show')
            ->layout('layouts.app');
    }
}