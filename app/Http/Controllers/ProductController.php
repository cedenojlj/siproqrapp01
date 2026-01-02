<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductWarehouse;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function uploadForm()
    {
        $warehouses = Warehouse::all();

        return view('upload', compact('warehouses'));
    }

    public function processCSV(Request $request)
    {
        // 1. Validar archivo
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
            'warehouse_id' => 'required|exists:warehouses,id'
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        // 2. Contadores
        $guardados = 0;
        $totalErrores = 0;
        $lineasConError = [];
        
        // 3. Abrir archivo
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return back()->with('error', 'No se pudo abrir el archivo');
        }
        
        // 4. Encabezados
        $encabezadosOriginales = [];
        if ($request->has('skip_header')) {
            $encabezadosOriginales = fgetcsv($handle);
        }
        
        if (empty($encabezadosOriginales)) {
            $encabezadosOriginales = ['name', 'sku', 'classification_id', 'type', 'size', 'GN', 'invoice_number'];
        }
        
        // 5. Procesar líneas
        $lineaNumero = 0;
        while (($fila = fgetcsv($handle)) !== false) {
            $lineaNumero++;
            $errorEnLinea = null;
            
            // Validar 6 columnas
            if (count($fila) != 6) {
                $errorEnLinea = "Tiene " . count($fila) . " columnas (se esperaban 6)";
            } else {
                // Validar campos
                $datos = [
                    'name' => trim($fila[0]),                    
                    'type' => trim($fila[1]),
                    'size' => trim($fila[2]),
                    'GN' => trim($fila[3]),
                    'cantidad' => trim($fila[4]),
                    'invoice_number' => trim($fila[5])
                ];
                
                // Campos requeridos
                $camposRequeridos = ['name','type', 'size', 'GN', 'cantidad', 'invoice_number'];
                foreach ($camposRequeridos as $campo) {
                    if (empty($datos[$campo])) {
                        $errorEnLinea = "Campo '$campo' vacío";
                        break;
                    }
                }
                
                // Validar números                
                
                if (!$errorEnLinea && !is_numeric(str_replace(',', '.', $datos['GN']))) {
                    $errorEnLinea = "GN no es número";
                }

                if (!$errorEnLinea && !is_numeric(str_replace(',', '.', $datos['cantidad']))) {
                    $errorEnLinea = "Cantidad no es número";
                }
            }
            
            // Manejar error
            if ($errorEnLinea) {
                $totalErrores++;
                $lineasConError[] = [
                    'linea' => $lineaNumero,
                    'datos' => $fila,
                    'error' => $errorEnLinea
                ];
                continue;
            }
            
            // Intentar guardar

            try {                                
                    $name = trim($fila[0]);                    
                    $type = trim($fila[1]);
                    $size = trim($fila[2]);
                    $GN = (float)str_replace(',', '.', trim($fila[3]));
                    $cantidad = (float)str_replace(',', '.', trim($fila[4]));
                    $invoice_number = trim($fila[5]);
                    $warehouse_id = (int)trim($request->input('warehouse_id'));

                                       
                    // $prueba=[
                    //     'name'=>$name,
                    //     'type'=>$type,
                    //     'size'=>$size,
                    //     'GN'=>$GN,
                    //     'cantidad'=>$cantidad,
                    //     'invoice_number'=>$invoice_number,
                    //     'warehouse_id'=>$warehouse_id
                    // ];
                    
                
                    
                $verificar=$this->createProduct($name, $type, $size, $GN, $invoice_number, $cantidad, $warehouse_id);             
                
                               
                if (!$verificar) {
                    // Error al crear el producto
                    throw new \Exception('Error al crear el producto');

                }else {
                    // Producto creado exitosamente
                    $guardados++;
                    continue;
                }
               

            } catch (\Exception $e) {
                
                $totalErrores++;                
                $lineasConError[] = [
                    'linea' => $lineaNumero,
                    'datos' => $fila,
                    'error' => 'Error BD: ' . (str_contains($e->getMessage(), 'Duplicate') ? 'SKU duplicado' : 'Error al guardar')
                ];
            }
        }
        
        fclose($handle);
        
        // 6. Preparar respuesta
        $responseData = [
            'guardados' => $guardados,
            'total_lineas' => $lineaNumero,
            'total_errores' => $totalErrores
        ];
        
        // 7. Si hay errores
        if ($totalErrores > 0) {
            $csvErrores = $this->generarCSVErrores($lineasConError, $encabezadosOriginales);
            
            // Opción A: Descarga inmediata
            if ($request->has('descargar_inmediato')) {
                return $this->descargarCSV($csvErrores);
            }
            
            // Opción B: Guardar en sesión
            session()->put('csv_base64', base64_encode($csvErrores));
            session()->put('nombre_archivo', 'errores_' . date('Y-m-d_H-i') . '.csv');
            $responseData['tiene_errores'] = true;
        }
        
        // 8. Retornar
        return back()->with($responseData);
    }
    
    private function generarCSVErrores($lineasConError, $encabezados)
    {
        $output = fopen('php://temp', 'w');
        fputcsv($output, array_merge(['Linea', 'Error'], $encabezados));
        
        foreach ($lineasConError as $linea) {
            $filaCSV = array_merge(
                [$linea['linea'], $linea['error']],
                $linea['datos']
            );
            fputcsv($output, $filaCSV);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
    
    private function descargarCSV($csvContent)
    {
        $filename = 'errores_' . date('Y-m-d_H-i') . '.csv';
        
        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    public function descargarErroresSession()
    {
        try {
            // 1. Verificar sesión
            if (!session()->has('csv_base64') || !session()->has('nombre_archivo')) {
                return redirect('/')->with('error', 
                    '❌ No hay archivo de errores disponible. Procesa un CSV primero.');
            }
            
            // 2. Obtener datos
            $csvBase64 = session('csv_base64');
            $filename = session('nombre_archivo');
            
            // 3. Validar base64
            $csvContent = base64_decode($csvBase64, true);
            if ($csvContent === false) {
                session()->forget(['csv_base64', 'nombre_archivo']);
                return redirect('/')->with('error', 
                    '⚠️ Archivo corrupto. Procesa nuevamente.');
            }
            
            // 4. Limpiar sesión
            session()->forget(['csv_base64', 'nombre_archivo']);
            
            // 5. Limpiar buffer
            if (ob_get_level()) {
                ob_clean();
                ob_end_clean();
            }
            
            // 6. Descargar
            return response($csvContent, 200, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($csvContent),
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0, max-age=0',
                'Expires' => '0'
            ]);
            
        } catch (\Exception $e) {
            return redirect('/')->with('error', 
                '⚠️ Error al descargar: ' . $e->getMessage());
        }
    }



    // Crear producto individualmente

         public function createProduct($name, $type, $size, $GN, $invoice_number, $cantidad, $warehouse_id)

         {

             return DB::transaction(function () use ($name, $type, $size, $GN, $invoice_number, $cantidad, $warehouse_id) {

                 // 1. Obtener la clasificación

                 $classification = Classification::where('code', $type)->first();

                 if (!$classification) {

                     // Lanzamos una excepción que será capturada en processCSV

                     throw new \Exception("Classification with code '$type' not found.");

                 }

                 $classification_id = $classification->id;

     

                 // 2. Crear el producto

                 $producto = Product::create([

                     'name' => $name,

                     'sku' => Str::upper(Str::random(20)), // SKU único temporal

                     'type' => $type,

                     'size' => $size,

                     'GN' => $GN,

                     'invoice_number' => $invoice_number,

                     'classification_id' => $classification_id,

                 ]);

     

                 // 3. Crear la entrada en el almacén

                 ProductWarehouse::create([

                     'product_id' => $producto->id,

                     'warehouse_id' => $warehouse_id,

                     'stock' => $cantidad,

                 ]);

     

                 // 4. Crear precios para cada cliente

                 $customers = Customer::all();

                 foreach ($customers as $customer) {

                     // Lógica de getQuery implementada directamente aquí

                     $dataPrecioQry = DB::table('prices')

                         ->where('customer_id', $customer->id)

                         ->whereIn('product_id', function ($query) use ($classification_id) {

                             $query->select('id')->from('products')->where('classification_id', $classification_id);

                         })

                         ->select('price_quantity as price_quantityQry', 'price_weight as price_weightQry')

                         ->first();

     

                     $precio_quantity = $dataPrecioQry->price_quantityQry ?? $producto->classification->precio_unidad ?? 0;

                     $precio_weight = $dataPrecioQry->price_weightQry ?? $producto->classification->precio_peso ?? 0;

     

                     $producto->prices()->create([

                         'customer_id' => $customer->id,

                         'price_quantity' => $precio_quantity,

                         'price_weight' => $precio_weight,

                     ]);

                 }

     

                 // Si todo fue bien dentro de la transacción, retornamos true.

                 return true;

             });

         }


}