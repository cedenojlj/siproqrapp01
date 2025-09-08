<?php

namespace App\Livewire;

use Livewire\Component;

class Escaneosku extends Component
{

    /**
     * Propiedad para almacenar el resultado y mostrarlo en la vista.
     */
    public string $scannedResult = '';

    /**
     * Esta es la acción que será llamada desde JavaScript.
     * Livewire se encarga de la comunicación automáticamente.
     *
     * @param string $decodedText El texto decodificado del QR.
     */
    public function processQrCodesku($decodedText)
    {
        // --- Aquí va tu lógica de negocio ---
        // Puedes buscar en la BD, validar un ticket, etc.
        // Por ahora, solo mostraremos un mensaje de éxito.

        if ($decodedText) {

           // $this->scannedResult = '✅ Código QR procesado correctamente: ' . $decodedText;

            $decoded = json_decode($decodedText, true);

            if (json_last_error() === JSON_ERROR_NONE && !empty($decoded['sku'])) {
                $this->dispatch('enviar-sku', $decoded['sku']);
                session()->flash('message', 'SKU encontrado: ' . $decoded['sku']);
            } else {
                //session()->flash('error', 'QR no contiene un JSON válido o falta el campo "sku".');
                $this->dispatch('skuInvalid');
            }
        } else {
            $this->scannedResult = '❌ Error: No se recibió ningún código.';
        }
    }

    public function render()
    {
        return view('livewire.escaneosku');
    }
}
