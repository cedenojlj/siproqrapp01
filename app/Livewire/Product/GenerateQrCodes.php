<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
//use Illuminate\Container\Attributes\DB;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class GenerateQrCodes extends Component
{
    public $products;
    public $selectedProducts = [];

    public function mount()
    {
        // OBTENER LOS PRODUCTOS EN ORDEN DESCENDENTE
        $this->products = Product::orderBy('id', 'desc')->get();

       // $this->products = DB::table('products')->orderBy('id', 'desc')->paginate(10);
       
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
         //$this->products = Product::paginate(10);
        return view('livewire.product.generate-qr-codes');
    }
}