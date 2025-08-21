<div>
    @if($abierto)
        <!-- Fondo -->
        <div class="modal-backdrop fade show"></div>
        
        <!-- Modal -->
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Lector de QR</h5>
                        <button class="btn-close" wire:click="cerrar"></button>
                    </div>
                    <div class="modal-body">

                        <livewire:product.qr-scanner />

                    </div>
                </div>
            </div>
    @endif
</div>
