
<!-- Modal Hapus Kategori Surat -->
<div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1"
    aria-labelledby="deleteKategoriModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="deleteKategoriModalLabel">
                    Hapus Kategori Surat
                </h5>

                <button type="button" class="close" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kategori surat berikut?</p>

                <div class="row mb-2">
                    <div class="col-4">Nama Kategori</div>
                    <div class="col-8">: {{ $nama_kategori }}</div>
                </div>

                <div class="row mb-2">
                    <div class="col-4">Kode Kategori</div>
                    <div class="col-8">: {{ $kode_kategori }}</div>
                </div>

                <div class="row mb-2">
                    <div class="col-4">Keterangan</div>
                    <div class="col-8">: {{ $keterangan ?: '-' }}</div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm"
                        data-dismiss="modal">
                        Batal
                    </button>

                    <button
                        wire:click="destroy({{ $kategorisurat_id }})"
                        type="button"
                        class="btn btn-danger btn-sm">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- End Modal Hapus Kategori Surat -->
