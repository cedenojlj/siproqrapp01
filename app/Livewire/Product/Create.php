<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use App\Models\Classification;
use Illuminate\Support\Str;
use App\Models\Warehouse;
use Livewire\Attributes\On; 

class Create extends Component
{
    public $name;
    public $description;
    public $sku;
    public $tamanio; // size in the database
    public $cantidad;
    public $classification_id;
    public $warehouse_id;
    public $type;
    

    protected $rules = [
        'name' => 'required|string|max:255',
        'sku' => 'required|string|max:255|unique:products,sku',
        'type' => 'required|string|max:255',
        'tamanio' => 'nullable', //size en la base de datos
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

     #[On('colocarCodigo')] 
    public function recibiendoTipo($idcodigo)
    {
        // Buscar en la tabla classifications por el campo code este valor existe

         $clasificacion = Classification::find($idcodigo);
        
         if ($clasificacion) {
           
            $this->name=$clasificacion->description;
            $this->type=$clasificacion->code;
            $this->tamanio=$clasificacion->size;    
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

        Product::create([
            'name' => $this->name,            
            'sku' => $this->sku,
            'type' => $this->type,
            'size' => $this->tamanio,
            'GN' => $this->GN,
            'GW' => $this->GW,
            'Box' => $this->Box,
            'invoice_number' => $this->invoice_number,
            'cantidad' => $this->cantidad,
            'classification_id' => $this->classification_id,
            'warehouse_id' => $this->warehouse_id,
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
            $this->description = $scannedData['description'] ?? '';
            $this->sku = $scannedData['sku'] ?? '';
            $this->tamanio = $scannedData['tamanio'] ?? '';
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