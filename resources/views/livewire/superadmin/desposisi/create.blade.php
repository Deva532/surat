{{-- Modal Tambah/Edit Disposisi --}}
<div
    wire:ignore.self
    class="modal fade"
    id="createModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="createModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            {{-- Header Modal --}}
            <div class="modal-header {{ $isEdit ? 'bg-warning' : 'bg-primary' }}">
                <h5 class="modal-title" id="createModalLabel">
                    <i class="fas {{ $isEdit ? 'fa-edit' : 'fa-plus' }} mr-1"></i>
                    {{ $isEdit ? 'Edit Disposisi' : 'Tambah Disposisi' }}
                </h5>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Tutup"
                    wire:click="tutupForm">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Form Disposisi --}}
            <form wire:submit="simpan">

                <div class="modal-body">

                    {{-- Surat Masuk --}}
                    <div class="form-group">
                        <label>Surat Masuk <span class="text-danger">*</span></label>

                        <select
                            class="form-control @error('surat_masuk_id') is-invalid @enderror"
                            wire:model="surat_masuk_id">

                            <option value="">-- Pilih Surat Masuk --</option>

                            @foreach ($suratMasuks as $surat)
                                <option value="{{ $surat->id }}">
                                    {{ $surat->nomor_surat }} - {{ $surat->perihal }}
                                </option>
                            @endforeach
                        </select>

                        @error('surat_masuk_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Pimpinan --}}
                    <div class="form-group">
                        <label>Pimpinan</label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $profil?->kepala_sekolah ?? '-' }}"
                            readonly>
                    </div>

                    {{-- Penerima Disposisi --}}
                    <div class="form-group">
                        <label>Penerima Disposisi <span class="text-danger">*</span></label>

                        <select
                            class="form-control @error('penerima_id') is-invalid @enderror"
                            wire:model="penerima_id">

                            <option value="">-- Pilih Penerima --</option>

                            @foreach ($penerimas as $penerima)
                                <option value="{{ $penerima->id }}">
                                    {{ $penerima->name }}
                                    - NIP: {{ $penerima->nip }}
                                    ({{ $penerima->role === 'superadmin' ? 'Admin' : 'Guru' }})
                                </option>
                            @endforeach
                        </select>

                        @error('penerima_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Isi Instruksi --}}
                    <div class="form-group">
                        <label>Isi Instruksi <span class="text-danger">*</span></label>

                        <textarea
                            class="form-control @error('isi_disposisi') is-invalid @enderror"
                            rows="3"
                            wire:model="isi_disposisi"
                            placeholder="Masukkan instruksi disposisi..."></textarea>

                        @error('isi_disposisi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Sifat Instruksi dan Batas Waktu --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sifat Instruksi <span class="text-danger">*</span></label>

                                <select
                                    class="form-control @error('sifat_instruksi') is-invalid @enderror"
                                    wire:model="sifat_instruksi">

                                    <option value="Biasa">Biasa</option>
                                    <option value="Penting">Penting</option>
                                    <option value="Sangat Segera">Sangat Segera</option>
                                    <option value="Sangat Segera">Rahasia</option>
                                </select>

                                @error('sifat_instruksi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Batas Waktu</label>

                                <input
                                    type="date"
                                    class="form-control @error('batas_waktu') is-invalid @enderror"
                                    wire:model="batas_waktu">

                                @error('batas_waktu')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>

                        <select
                            class="form-control @error('status') is-invalid @enderror"
                            wire:model="status">

                            <option value="Belum Dibaca">Belum Dibaca</option>
                            <option value="Proses">Proses</option>
                            <option value="Selesai">Selesai</option>
                        </select>

                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Catatan Tindak Lanjut --}}
                    <div class="form-group mb-0">
                        <label>Catatan Tindak Lanjut</label>

                        <textarea
                            class="form-control @error('catatan_tindak_lanjut') is-invalid @enderror"
                            rows="3"
                            wire:model="catatan_tindak_lanjut"
                            placeholder="Tambahkan catatan tindak lanjut jika ada..."></textarea>

                        @error('catatan_tindak_lanjut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                {{-- Footer Modal --}}
                <div class="modal-footer justify-content-between">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                        wire:click="tutupForm">
                        <i class="fas fa-times mr-1"></i>
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        wire:loading.attr="disabled"
                        wire:target="simpan">

                        <span wire:loading.remove wire:target="simpan">
                            <i class="fas fa-save mr-1"></i>
                            {{ $isEdit ? 'Perbarui' : 'Simpan' }}
                        </span>

                        <span wire:loading wire:target="simpan">
                            <i class="fas fa-spinner fa-spin mr-1"></i>
                            Menyimpan...
                        </span>
                    </button>

                </div>
            </form>

        </div>
    </div>
</div>