<?php

namespace App\Livewire;

use Livewire\Component;

class Escaneo extends Component
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
    public function processQrCode(string $decodedText)
    {
        // --- Aquí va tu lógica de negocio ---
        // Puedes buscar en la BD, validar un ticket, etc.
        // Por ahora, solo mostraremos un mensaje de éxito.

        if ($decodedText) {
            //$this->scannedResult = '✅ Código QR procesado correctamente: ' . $decodedText;
            $parsed = $this->parseAndValidate($decodedText);
           // dd($parsed);

            if ($parsed['valid']) {
                $this->dispatch('qrValidated', $parsed['data']);
                if (session()->has('warning')) {
                    session()->reflash(); // Mantener advertencia
                }
            } else {
                //session()->flash('error', $parsed['error']);
                $this->dispatch('qrInvalid', $parsed['error']);
            }
        } else {
            $this->scannedResult = '❌ Error: No se recibió ningún código.';
        }
    }


    private function parseAndValidate($text)
    {
        $pairs = [];

        // --- 1. Detectar separador ---
        if (str_contains($text, '&') && !str_starts_with($text, 'HTTP')) {
            $items = explode('&', $text);
        } elseif (str_contains($text, '|')) {
            $items = explode('|', $text);
        } elseif (str_contains($text, ';')) {
            $items = explode(';', $text);
        } elseif (preg_match("/[\r\n]+/", $text)) {
            $items = preg_split("/[\r\n]+/", $text);
        } else {
            $items = [$text];
        }

        // --- 2. Parsear cada item ---
        foreach ($items as $item) {
            $item = trim($item);
            if (empty($item)) continue;

            if (strpos($item, '=') !== false) {
                [$key, $value] = array_map('trim', explode('=', $item, 2));
            } elseif (strpos($item, ':') !== false) {
                [$key, $value] = array_map('trim', explode(':', $item, 2));
            } elseif (preg_match('/^(\w+)\s+(.+)$/', $item, $matches)) {
                $key = $matches[1];
                $value = $matches[2];
            } else {
                continue;
            }

            $pairs[$key] = $value;
        }

        // --- 3. Definir campos ---
        $required = ['TYPE', 'SIZE', 'INVOICE'];
       // $optional = ['NAME','G.N.', 'G.W', 'BOX'];
        $optional = ['NAME','G.N.'];
        $allFields = array_merge($required, $optional);

        $missingRequired = [];

        foreach ($required as $field) {
            if (!isset($pairs[$field])) {
                $missingRequired[] = $field;
            }
        }

        // Si faltan obligatorios → error
        if (!empty($missingRequired)) {
            return [
                'valid' => false,
                'error' => 'Faltan campos obligatorios: ' . implode(', ', $missingRequired)
            ];
        }

        // Verificar opcionales para advertencia
        $missingOptional = array_filter($optional, fn($f) => !isset($pairs[$f]));
        if (!empty($missingOptional)) {
            session()->flash('warning', 'Campos no encontrados (opcionales): ' . implode(', ', $missingOptional));
        }

        // Completar todos los campos (los faltantes serán vacíos)
        $filledData = [];
        foreach ($allFields as $field) {
            $filledData[$field] = $pairs[$field] ?? '';
        }

        return [
            'valid' => true,
            'data' => $filledData
        ];
    }

    public function render()
    {
        return view('livewire.escaneo');
    }
}
