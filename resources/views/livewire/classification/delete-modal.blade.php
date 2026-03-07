<div class="modal fade show" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" style="display: block;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Classification</h5>
                <button type="button" class="btn-close" wire:click="closeDeleteModal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this classification?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeDeleteModal">Cancel</button>
                <button type="button" wire:click.prevent="delete()" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
