
<!-- Modal Kategori Surat -->
<div wire:ignore.self class="modal fade" id="createModal" tabindex="-1"
    aria-labelledby="kategoriSuratModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="kategoriSuratModalLabel">
                    Kategori Surat
                </h5>

                <button type="button" class="close" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                {{-- Nama Kategori --}}
                <div class="form-group">
                    <label for="nama_kategori">
                        Nama Kategori Surat
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        wire:model="nama_kategori"
                        type="text"
                        id="nama_kategori"
                        class="form-control form-control-sm @error('nama_kategori') is-invalid @enderror"
                        placeholder="Masukkan nama kategori surat">

                    @error('nama_kategori')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                {{-- Kode Kategori --}}
                <div class="form-group">
                    <label for="kode_kategori">
                        Kode Kategori
                        <span class="text-danger">*</span>
                    </label>
                    <input
                        wire:model="kode_kategori"
                        type="text"
                        id="kode_kategori"
                        class="form-control form-control-sm @error('kode_kategori') is-invalid @enderror"
                        placeholder="Masukkan kode kategori">
                    @error('kode_kategori')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                {{-- Keterangan --}}
                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea
                        wire:model="keterangan"
                        id="keterangan"
                        rows="3"
                        class="form-control form-control-sm @error('keterangan') is-invalid @enderror"
                        placeholder="Masukkan keterangan (opsional)"></textarea>
                    @error('keterangan')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm"
                    data-dismiss="modal">
                    Tutup
                </button>
                <button wire:click="store" type="button"
                    class="btn btn-primary btn-sm">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>

        </div>
    </div>
</div>
<!-- End Modal Kategori Surat -->
