<div>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5>🔍 Escáner de QR (Flexible)</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <button type="button" class="btn btn-success me-2" onclick="startScan()">
                    📷 Encender
                </button>
                <button type="button" class="btn btn-danger" onclick="stopScan()">
                    🔴 Apagar
                </button>
            </div>

            <div id="qr-reader" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
            <div id="qr-reader-results" class="mt-3"></div>
        </div>
    </div>
</div>

@script
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        let html5QrcodeScanner = null;

        function startScan() {
            const successCallback = (decodedText) => {
                const div = document.getElementById('qr-reader-results');
                div.innerHTML = `<div class="alert alert-info small">Leído: <code>${decodedText}</code></div>`;
                @this.setResult(decodedText);
                stopScan();
            };

            const errorCallback = (err) => {
                console.warn(`Error: ${err}`);
            };

            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear();
            }

            html5QrcodeScanner = new Html5QrcodeScanner(
                "qr-reader", {
                    fps: 10,
                    qrbox: 250
                },
                false
            );
            html5QrcodeScanner.render(successCallback, errorCallback);
        }

        function stopScan() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear().catch(console.error);
                html5QrcodeScanner = null;
            }
        }
    </script>
@endscript
