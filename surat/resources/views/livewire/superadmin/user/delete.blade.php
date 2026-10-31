<!-- Modal -->
<div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Hapus User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-4">
                        Nama
                    </div>
                    <div class="col-8">
                        : {{ $name }}
                    </div>
                    <div class="col-4">
                        Email
                    </div>
                    <div class="col-8">
                        : {{ $email }}
                    </div>
                    <div class="col-4">
                        Role
                    </div>
                    <div class="col-8">
                        : {{ $role }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                    <button wire:click="destroy({{ $user_id }})" type="button"
                        class="btn btn-danger btn-sm">Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Modal -->
