<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Livewire\WithPagination;

class FilterableTable extends Component
{
    use WithPagination;

    public $filterSku = '';
    public $filterInvoiceNumber = '';
    public $filterDate = '';

    public function render()
    {
        $products = Product::query()
            ->when($this->filterSku, fn($query, $sku) => $query->where('sku', 'like', '%' . $sku . '%'))
            ->when($this->filterInvoiceNumber, fn($query, $invoice) => $query->where('invoice_number', 'like', '%' . $invoice . '%'))
            ->when($this->filterDate, fn($query, $date) => $query->whereDate('created_at', $date))
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.product.filterable-table', [
            'products' => $products,
        ]);
    }

    public function downloadQrPdf(Product $product)
    {
        $pdf = Pdf::loadView('pdf.product-qrcodes', ['products' => collect([$product])]);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'qr-'.$product->sku.'.pdf');
    }

    /* public function downloadQrImage(Product $product)
    {
        $qrCode = QrCode::format('png')->size(200)->generate($product->sku);
        return response()->streamDownload(function () use ($qrCode) {
            echo $qrCode;
        }, 'qr-'.$product->sku.'.png');
    } */
}
