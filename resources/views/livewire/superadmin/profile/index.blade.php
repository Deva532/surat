<div>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>
                            <i class="fas fa-sitemap mr-2"></i>
                            {{ $title }}
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">
                                    <i class="fas fa-home mr-1"></i>
                                    Dashboard
                                </a></li>
                            <li class="breadcrumb-item active">
                                {{ $title }}
                            </li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="card">
                {{-- <div class="card-header">
                </div> --}}
                <div class="card-body">
                    <div>
                        @if (session()->has('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        <form wire:submit.prevent="update" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label>Nama Instansi</label>
                                <input type="text" class="form-control" wire:model.live="instansi">
                                @error('instansi') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="mb-3">
                                <label>Nama Dinas</label>
                                <input type="text" class="form-control" wire:model.live="dinas">
                                @error('dinas') <small class="text-danger">{{ $message }}</small> @enderror
                            <div class="mb-3">
                                <label>Nama Lembaga</label>
                                <input type="text" class="form-control" wire:model.live="nama_sekolah">
                                @error('nama_sekolah') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="mb-3">
                                <label>Kode Lembaga</label>
                                <input type="text" class="form-control" wire:model.live="kode_lembaga">
                                @error('nama_sekolah') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="mb-3">
                                <label>Alamat</label>
                                <textarea class="form-control" wire:model.live="alamat"></textarea>
                                @error('alamat') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="mb-3">
                                <label>Kecamatan</label>
                                <textarea class="form-control" wire:model.live="kecamatan"></textarea>
                                @error('kecamatan') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="mb-3">
                                <label>Kabupaten</label>
                                <textarea class="form-control" wire:model.live="kabupaten"></textarea>
                                @error('kabupaten') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="mb-3">
                                <label>Telepon</label>
                                <input type="text" class="form-control" wire:model.live="telepon">
                            </div>
                            <div class="mb-3">
                                <label>Email Lembaga</label>
                                <input type="email" class="form-control" wire:model.live="email">
                            </div>
                            <div class="mb-3">
                                <label>Nama Kepala</label>
                                <input type="text" class="form-control" wire:model.live="kepala_sekolah">
                            </div>
                            <div class="form-group">
                                <label>NIP Kepala </label>
                                <input type="text" class="form-control" wire:model="nip_kepala_sekolah"
                                    placeholder="Masukkan NIP Kepala Sekolah">
                            </div>
                            <div class="form-group">
                                <label>Status Kepala</label>
                                <select class="form-control" wire:model="status">
                                    <option value="">Pilih Status</option>
                                    <option value="Definitif">Definitif</option>
                                    <option value="Plt">Plt</option>
                                </select>
                            </div>

                            {{-- ✅ LOGO SEKOLAH --}}
                            <div class="mb-3">
                                <label>Logo Sekolah</label>
                                <input type="file" class="form-control" wire:model.live="logo">
                                @if ($logo_lama)
                                    <img src="{{ asset('storage/' . $logo_lama) }}" height="70" class="mt-2">
                                @endif
                                @error('logo') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            {{-- ✅ LOGO PEMDA --}}
                            <div class="mb-3">
                                <label>Logo Pemerintah Daerah</label>
                                <input type="file" class="form-control" wire:model.live="logo_pemda">
                                @if ($logo_pemda_lama)
                                    <img src="{{ asset('storage/' . $logo_pemda_lama) }}" height="70" class="mt-2">
                                @endif
                                @error('logo_pemda') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <button class="btn btn-primary">
                                Simpan Profil
                            </button>
                        </form>
                    </div>
                </div>
                <!-- /.card-body -->
                <!-- /.card-footer-->
            </div>
            <!-- /.card -->
        </section>
    </div>
    {{-- Because she competes with no one, no one can compete with her. --}}
</div>