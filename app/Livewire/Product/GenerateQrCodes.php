<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateQrCodes extends Component
{
    public $products;
    public $selectedProducts = [];

    public function mount()
    {
        $this->products = Product::all();
    }

    public function generatePdf()
    {
        $selectedProductsData = Product::whereIn('id', $this->selectedProducts)->get();

        if ($selectedProductsData->isEmpty()) {
            session()->flash('error', 'Please select at least one product to generate QR codes.');
            return;
        }

        $pdf = Pdf::loadView('pdf.product-qrcodes', ['products' => $selectedProductsData]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'product_qrcodes.pdf');
    }

    public function render()
    {
        return view('livewire.product.generate-qr-codes')
            ->layout('layouts.app');
    }
}