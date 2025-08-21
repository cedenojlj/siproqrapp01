<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use App\Models\Classification;
use App\Models\ProductWarehouse;
use Illuminate\Support\Str;
use App\Models\Warehouse;
use Livewire\Attributes\On; 

class Create extends Component
{
    public $name;    
    public $sku;
    public $size; // size in the database
    public $cantidad=1;
    public $classification_id;   
    public $type;
    public $GN;
    public $GW;
    public $Box;
    public $invoice_number;
    public $product_id; // Added to store the product ID if needed
    public $warehouse_id; // Added to store the selected warehouse ID
    

    protected $rules = [
        'name' => 'required|string|max:255',
        'sku' => 'required|string|max:255|unique:products,sku',
        'type' => 'required|string|max:255',
        'size' => 'nullable', //size en la base de datos
        //'GN' => 'nullable',
        //'GW' => 'nullable',
        //'Box' => 'nullable',
        //'invoice_number' => 'nullable|string|',
        'cantidad' => 'required|integer|min:0',
        'classification_id' => 'required|exists:classifications,id',
        'warehouse_id' => 'required|exists:warehouses,id',
    ];

    public function mount()
    {
        $this->cantidad=1;
    }

    //Para abrir el scanner
    public function scanner()
    {
        $this->dispatch('openScannerModal');
    }

    #[On('qrValidated')] 
    function leerScanner($data) {

        $this->type = $data['type'] ?? '';
        $this->size = $data['size'] ?? '';
        $this->GN = $data['GN'] ?? '';
        $this->GW = $data['GW'] ?? '';
        $this->Box = $data['Box'] ?? '';
        $this->invoice_number = $data['invoice'] ?? '';
        
    }

    #[On('qrInvalid')] 
    function manejarErrorQr($error) {
        $this->dispatch('showError', $error);
         session()->flash('error', $error);
    }

     #[On('colocarCodigo')] 
    public function recibiendoTipo($idcodigo)
    {
        // Buscar en la tabla classifications por el campo code este valor existe

         $clasificacion = Classification::find($idcodigo);
        
         if ($clasificacion) {
           
            $this->name=$clasificacion->description;
            $this->type=$clasificacion->code;
            $this->size=$clasificacion->size;    
            $this->classification_id = $clasificacion->id;

        }
 
    }

    //funcion para crear sku

    public function crearSku()
    {
        //convertir a mayuscula 
        $this->sku = Str::upper(Str::random(20));
        //$this->validateOnly('sku');
    }

    public function updatingType($type)
    {
       //crear funcion que busque en la tabla classifications por el campo code este valor existe   
       // dd($type);
       $this->resetValidation('type');
       $classification = Classification::where('code', $type)->first();

       //dd($classification);

       if ($classification) {

           $this->type = $type;
           $this->classification_id = $classification->id;

       }else{
           
           $this->addError('type', 'Classification not found for the provided code.');
           $this->type = null;
           $this->classification_id = null;
       }
    }

    public function save()
    {
        $this->validate();

       $producto =Product::create([
            'name' => $this->name,            
            'sku' => $this->sku,        
            'type' => $this->type,
            'size' => $this->size,
            'GN' => $this->GN,
            'GW' => $this->GW,
            'Box' => $this->Box,
            'invoice_number' => $this->invoice_number,            
            'classification_id' => $this->classification_id,
        ]);

        //crear un productwarehouses
        ProductWarehouse::create([
            'product_id' => $producto->id,
            'warehouse_id' => $this->warehouse_id,
            'stock' => $this->cantidad, // Assuming initial stock is 0
        ]);

        session()->flash('message', 'Product created successfully.');

        $this->reset(); // Clear form fields after saving

        return redirect()->route('products.index');
    }

    public function render()
    {
        $classifications = Classification::all();
        $warehouses = Warehouse::all();

        return view('livewire.product.create', [
            'classifications' => $classifications,
            'warehouses' => $warehouses,
        ]);
    }

    public function fillFormFromQrCode($data)
    {
        $scannedData = json_decode($data, true);

        if ($scannedData) {
            $this->name = $scannedData['name'] ?? '';            
            $this->sku = $scannedData['sku'] ?? '';
            $this->size = $scannedData['size'] ?? '';
            $this->cantidad = $scannedData['cantidad'] ?? 0;
            $this->classification_id = $scannedData['classification_id'] ?? '';
            $this->warehouse_id = $scannedData['warehouse_id'] ?? '';

            $this->dispatch('qr-scanned-success'); // Emit event for UI feedback
            session()->flash('qr_message', 'QR code scanned successfully!');
        } else {
            session()->flash('qr_error', 'Invalid QR code data.');
        }
    }
}