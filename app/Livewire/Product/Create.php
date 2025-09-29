<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use App\Models\Classification;
use App\Models\ProductWarehouse;
use App\Models\Customer;
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
    //public $GW;
    //public $Box;
    public $invoice_number;
    public $product_id; // Added to store the product ID if needed
    public $warehouse_id; // Added to store the selected warehouse ID
    public $customers;

    protected $rules = [
        'name' => 'required|string|max:255',
        'sku' => 'required|string|max:255|unique:products,sku',
        'type' => 'required|string|max:255|exists:classifications,code',
        'size' => 'required|string|max:255', //size en la base de datos
        'GN' => 'required|numeric|min:0',
        // 'GW' => 'required|string|max:255',
        // 'Box' => 'required|string|max:255',
        'invoice_number' => 'required|string|max:255',
        'cantidad' => 'required|numeric|min:0',
        'classification_id' => 'required|exists:classifications,id',
        'warehouse_id' => 'required|exists:warehouses,id',
    ];

    public function mount()
    {
        $this->cantidad=1;
        $this->customers = Customer::all();
    }

    //Para abrir el scanner
    public function scanner()
    {
        $this->dispatch('openScannerModal');
    }

    #[On('qrValidated')] 
    function leerScanner($data) {

        //dd($data);
        $this->name = $data['NAME'] ?? '';
        $this->type = $data['TYPE'] ?? '';
        $this->size = $data['SIZE'] ?? '';
        $this->GN = $data['G.N.'] ?? '';
        // $this->GW = $data['G.W'] ?? '';
        // $this->Box = $data['BOX'] ?? '';
        $this->invoice_number = $data['INVOICE'] ?? '';
        
    }

    #[On('qrInvalid')] 
    function manejarErrorQr($error) {
       // $this->dispatch('showError', $error);
      // dd('estoy en el error');
         session()->flash('qr_error', $error);
         $this->dispatch('cerrarScanner');
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
        //crear producto
       
       //dame el id de la tabla classifications donde el campo code sea igual a $this->type
        $classification = Classification::where('code', $this->type)->first();  
        if (!$classification) {
            $this->addError('type', 'Classification not found for the provided code.');
            return;
        }
        $this->classification_id = $classification->id;
        
        
         /* dd([
            'name' => $this->name,            
            'sku' => $this->sku,        
            'type' => $this->type,
            'size' => $this->size,
            'GN' => $this->GN,
            'GW' => $this->GW,
            'Box' => $this->Box,
            'invoice_number' => $this->invoice_number,            
            'classification_id' => $this->classification_id,
        ]);*/
       
       
       
        $this->validate();
        
       $producto =Product::create([
            'name' => $this->name,            
            'sku' => $this->sku,        
            'type' => $this->type,
            'size' => $this->size,
            'GN' => $this->GN,
            // 'GW' => $this->GW,
            // 'Box' => $this->Box,
            'invoice_number' => $this->invoice_number,            
            'classification_id' => $this->classification_id,
        ]);

        //crear un productwarehouses
        ProductWarehouse::create([
            'product_id' => $producto->id,
            'warehouse_id' => $this->warehouse_id,
            'stock' => $this->cantidad, // Assuming initial stock is 0
        ]);

        //crear precios para cada cliente
        $customers = Customer::all();
        foreach ($customers as $customer) {
            $producto->prices()->create([
                'customer_id' => $customer->id,
                'price_quantity' => $producto->classification->precio_unidad ?? 0,
                'price_weight' => $producto->classification->precio_peso ?? 0,
            ]);
        }

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