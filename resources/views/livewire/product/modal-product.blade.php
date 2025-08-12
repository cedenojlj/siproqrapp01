<div>

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-secondary mt-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Listado
    </button>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Listado de Codigos</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                   
                    <livewire:product.lista />
                </div>
                
            </div>
        </div>
    </div>
</div>
