<div>
    <style>
        #reader {
            max-width: 500px;
            margin: auto;
        }

        .result-container {
            text-align: center;
            font-size: 1.2rem;
            margin-top: 20px;
            font-weight: bold;
        }

        .loading-indicator {
            font-style: italic;
            color: #555;
        }
        
         h3 {
            text-align: center;
        }
        
    </style>

    <h3>Escáner de Código QR </h3>
    
    <div id="reader"></div>

    <div class="result-container">
        <div wire:loading class="loading-indicator">
            Procesando...
        </div>

        <div wire:loading.remove>
            {{ $scannedResult }}
        </div>

        <button wire:click="$dispatch('abrirScanner')" class="btn btn-secondary "><i class="bi bi-qr-code-scan"></i></button>

        <button wire:click="$dispatch('cerrarScanner')" class="btn btn-secondary "><i class="bi bi-x-square"></i></button>
    </div>


</div>
@script
    <script>
        // Asegurarnos de que el script se ejecuta después de que Livewire se inicie
        document.addEventListener('livewire:initialized', () => {

            let html5QrcodeScanner = null; // Declarar la variable para el escáner
            console.log('estoy adentro de initialized');

            // Función que se ejecuta cuando el escaneo es exitoso
            function onScanSuccess(decodedText, decodedResult) {
                // Pausar el escáner para evitar múltiples envíos
                html5QrcodeScanner.pause();

                // 3. LLAMAR A LA ACCIÓN DE LIVEWIRE
                // Aquí está la magia. En lugar de fetch(), usamos el objeto $wire
                // para llamar al método 'processQrCodesku' en nuestra clase de PHP.
                @this.call('processQrCodesku', decodedText);
            }

            // Función para manejar errores (opcional)
            function onScanFailure(error) {
                // Podemos ignorar los errores para una mejor experiencia de usuario
            }

            // 4. Escuchar el evento personalizado para reanudar el escáner

            Livewire.on('abrirScanner', () => {
                console.log('Reanudando el escáner...');
                // 2. Inicializar el escáner
               html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader", {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        }
                    },
                    /* verbose= */
                    false
                );

                html5QrcodeScanner.render(onScanSuccess, onScanFailure);

            });

            Livewire.on('cerrarScanner', () => {
                console.log('Deteniendo el escáner...');
                html5QrcodeScanner.clear().then(_ => {
                    // El escáner se ha detenido y limpiado
                    console.log('Escáner detenido y limpiado.');
                }).catch(error => {
                    console.error('Error al detener el escáner: ', error);
                });
            });


        });
    </script>
@endscript

