<div>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <h1>Surat Keluar</h1>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">

                @if (session()->has('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Buat Surat Keluar</h3>
                    </div>

                    <form wire:submit="simpan">
                        <div class="card-body">

                            <div class="form-group">
                                <label>Kategori Surat</label>
                                <select class="form-control @error('kategori_id') is-invalid @enderror"
                                    wire:model="kategori_id">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($kategoriSurat as $kategori)
                                        <option value="{{ $kategori->id }}">
                                            {{ $kategori->nama_kategori }}
                                            ({{ $kategori->kode_kategori }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Tanggal Surat</label>
                                <input type="date" class="form-control @error('tanggal_surat') is-invalid @enderror"
                                    wire:model="tanggal_surat">
                                @error('tanggal_surat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Penerima</label>
                                <input type="text" class="form-control @error('penerima') is-invalid @enderror"
                                    wire:model="penerima" placeholder="Nama penerima surat">
                                @error('penerima')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Alamat Tujuan</label>
                                <textarea class="form-control @error('alamat_tujuan') is-invalid @enderror"
                                    wire:model="alamat_tujuan" rows="2"
                                    placeholder="Alamat penerima (opsional)"></textarea>
                                @error('alamat_tujuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Perihal</label>
                                <input type="text" class="form-control @error('perihal') is-invalid @enderror"
                                    wire:model="perihal" placeholder="Perihal surat">
                                @error('perihal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Isi Surat</label>
                                <textarea class="form-control @error('isi_surat') is-invalid @enderror"
                                    wire:model="isi_surat" rows="10"
                                    placeholder="Tuliskan isi surat di sini..."></textarea>
                                @error('isi_surat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Sifat Surat</label>
                                <select class="form-control" wire:model="sifat">
                                    <option value="Biasa">Biasa</option>
                                    <option value="Penting">Penting</option>
                                    <option value="Rahasia">Rahasia</option>
                                    <option value="Segera">Segera</option>
                                </select>
                                @error('sifat')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="simpan">
                                    <i class="fas fa-save"></i> Simpan Draft
                                </span>
                                <span wire:loading wire:target="simpan">
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Surat Keluar</h3>
                    </div>

                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nomor Surat</th>
                                    <th>Tanggal</th>
                                    <th>Penerima</th>
                                    <th>Perihal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($suratKeluar as $index => $surat)
                                    <tr wire:key="surat-{{ $surat->id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $surat->nomor_surat }}</td>
                                        <td>
                                            {{ $surat->tanggal_surat?->format('d/m/Y') }}
                                        </td>
                                        <td>{{ $surat->penerima }}</td>
                                        <td>{{ $surat->perihal }}</td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                {{ $surat->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('superadmin.surat-keluar.cetak', $surat->id) }}"
                                                target="_blank" class="btn btn-sm btn-success">
                                                <i class="fas fa-print"></i>
                                                Cetak / PDF
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            Belum ada surat keluar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </section>
    </div>

</div>