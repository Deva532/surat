<!-- Modal Tambah Surat Masuk -->
<div wire:ignore.self class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createSuratMasukLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createSuratMasukLabel">
                    Tambah Surat Masuk
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- Nomor Agenda --}}
                <div class="form-group">
                    <label for="nomor_agenda">Nomor Agenda <span class="text-danger">*</span></label>
                    <input wire:model="nomor_agenda" type="text" id="nomor_agenda"
                        class="form-control form-control-sm @error('nomor_agenda') is-invalid @enderror" readonly>
                    @error('nomor_agenda')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                {{-- Nomor Surat --}}
                <div class="form-group">
                    <label for="nomor_surat">Nomor Surat <span class="text-danger">*</span></label>
                    <input wire:model="nomor_surat" type="text" id="nomor_surat"
                        class="form-control form-control-sm @error('nomor_surat') is-invalid @enderror"
                        placeholder="Masukkan nomor surat">
                    @error('nomor_surat')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                {{-- Tanggal Surat --}}
                <div class="form-group">
                    <label for="tanggal_surat">Tanggal Surat <span class="text-danger">*</span></label>
                    <input wire:model="tanggal_surat" type="date" id="tanggal_surat"
                        class="form-control form-control-sm @error('tanggal_surat') is-invalid @enderror">
                    @error('tanggal_surat')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                {{-- Tanggal Diterima --}}
                <div class="form-group">
                    <label for="tanggal_diterima">Tanggal Diterima <span class="text-danger">*</span></label>
                    <input wire:model="tanggal_diterima" type="date" id="tanggal_diterima"
                        class="form-control form-control-sm @error('tanggal_diterima') is-invalid @enderror">
                    @error('tanggal_diterima')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                {{-- Pengirim --}}
                <div class="form-group">
                    <label for="pengirim">Asal Surat / Pengirim <span class="text-danger">*</span></label>
                    <input wire:model="pengirim" type="text" id="pengirim"
                        class="form-control form-control-sm @error('pengirim') is-invalid @enderror"
                        placeholder="Masukkan asal surat">
                    @error('pengirim')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                {{-- Perihal --}}
                <div class="form-group">
                    <label for="perihal">Perihal / Hal <span class="text-danger">*</span></label>
                    <textarea wire:model="perihal" id="perihal" rows="3"
                        class="form-control form-control-sm @error('perihal') is-invalid @enderror"
                        placeholder="Masukkan perihal surat"></textarea>
                    @error('perihal')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                {{-- Kategori Surat --}}
                <div class="form-group">
                    <label for="kategori_id">
                        Kategori Surat <span class="text-danger">*</span>
                    </label>
                    <select wire:model="kategori_id" id="kategori_id"
                        class="form-control form-control-sm @error('kategori_id') is-invalid @enderror">
                        <option value="">-- Pilih Kategori Surat --</option>
                        @foreach (\Illuminate\Support\Facades\DB::table('kategori_surat')->orderBy('nama_kategori')->get() as $kategori)
                            <option value="{{ $kategori->id }}">
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                {{-- Sifat Surat --}}
                <div class="form-group">
                    <label for="sifat">Sifat Surat <span class="text-danger">*</span></label>
                    <select wire:model="sifat" id="sifat"
                        class="form-control form-control-sm @error('sifat') is-invalid @enderror">
                        <option value="Biasa">Biasa</option>
                        <option value="Penting">Penting</option>
                        <option value="Rahasia">Rahasia</option>
                        <option value="Segera">Segera</option>
                    </select>
                    @error('sifat')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                {{-- Penerima --}}
                <div class="form-group">
                    <label for="penerima_dituju">Penerima / Ditujukan Kepada</label>
                    <input wire:model="penerima_dituju" type="text" id="penerima_dituju"
                        class="form-control form-control-sm @error('penerima_dituju') is-invalid @enderror"
                        placeholder="Masukkan nama penerima">
                    @error('penerima_dituju')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                {{-- File PDF --}}
                <div class="form-group">
                    <label for="file">File Surat (PDF) <span class="text-danger">*</span></label>
                    <input wire:model="file" type="file" id="file" accept=".pdf,application/pdf"
                        class="form-control form-control-sm @error('file') is-invalid @enderror">
                    <small class="form-text text-muted">Format PDF, maksimal 2 MB.</small>
                    @error('file')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <div wire:loading wire:target="file" class="text-info mt-1">
                        Mengunggah file...
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    Tutup
                </button>
                <button wire:click="store" type="button" class="btn btn-primary btn-sm" wire:loading.attr="disabled"
                    wire:target="store,file">
                    <i class="fas fa-save mr-1"></i>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
<!-- End Modal Tambah Surat Masuk -->