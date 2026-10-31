<div>
    <div class="content-wrapper">
        {{-- Header halaman --}}
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Disposisi Surat Masuk</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ url('/superadmin') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Disposisi</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        {{-- Konten halaman --}}
        <section class="content">
            <div class="container-fluid">
                {{-- Notifikasi --}}
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif
                {{-- Informasi pimpinan --}}
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-tie mr-1"></i>
                            Informasi Pimpinan
                        </h3>
                    </div>
                    <div class="card-body">
                        @if ($profil)
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Nama Pimpinan</strong>
                                    <p>{{ $profil->kepala_sekolah ?: '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>NIP Pimpinan</strong>
                                    <p>{{ $profil->nip_kepala_sekolah ?: '-' }}</p>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                Data profil pimpinan belum tersedia.
                            </div>
                        @endif
                    </div>
                </div>
                {{-- Daftar disposisi --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-list mr-1"></i>
                            Daftar Disposisi
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" wire:click="create">
                                <i class="fas fa-plus mr-1"></i>
                                Tambah Data
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Pencarian dan filter --}}
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <input type="text" class="form-control"
                                    placeholder="Cari nomor surat, perihal, instruksi..."
                                    wire:model.live.debounce.300ms="search">
                            </div>
                            <div class="col-md-3 mb-2">
                                <select class="form-control" wire:model.live="filterStatus">
                                    <option value="">Semua Status</option>
                                    <option value="Belum Dibaca">Belum Dibaca</option>
                                    <option value="Proses">Proses</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                            </div>
                        </div>
                        {{-- Tabel --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="45">No.</th>
                                        <th>Nomor Surat</th>
                                        <th>Perihal</th>
                                        <th>Penerima</th>
                                        <th>Instruksi</th>
                                        <th>Sifat</th>
                                        <th>Batas Waktu</th>
                                        <th>Status</th>
                                        <th width="150">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($disposisis as $index => $item)
                                        <tr wire:key="disposisi-{{ $item->id }}">
                                            <td>{{ $disposisis->firstItem() + $index }}</td>
                                            <td>
                                                {{ $item->suratMasuk?->nomor_surat ?? '-' }}
                                            </td>
                                            <td>
                                                {{ $item->suratMasuk?->perihal ?? '-' }}
                                            </td>
                                            <td>
                                                {{ $item->penerima?->name ?? '-' }}
                                            </td>
                                            <td>{{ $item->isi_disposisi }}</td>
                                            <td>
                                                @if ($item->sifat_instruksi === 'Sangat Segera')
                                                    <span class="badge badge-danger">Sangat Segera</span>
                                                @elseif ($item->sifat_instruksi === 'Penting')
                                                    <span class="badge badge-warning">Penting</span>
                                                @else
                                                    <span class="badge badge-secondary">Biasa</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $item->batas_waktu?->format('d-m-Y') ?? '-' }}
                                            </td>
                                            <td>
                                                @if ($item->status === 'Selesai')
                                                    <span class="badge badge-success">Selesai</span>
                                                @elseif ($item->status === 'Proses')
                                                    <span class="badge badge-primary">Proses</span>
                                                @else
                                                    <span class="badge badge-warning">Belum Dibaca</span>
                                                @endif
                                            </td>
                                            <td class="text-nowrap">
                                                <button type="button" class="btn btn-warning btn-xs"
                                                    wire:click="edit({{ $item->id }})" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                @if ($item->status === 'Belum Dibaca')
                                                    <button type="button" class="btn btn-primary btn-xs"
                                                        wire:click="ubahStatus({{ $item->id }}, 'Proses')"
                                                        wire:confirm="Mulai memproses disposisi ini?" title="Mulai Proses">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                @elseif ($item->status === 'Proses')
                                                    <button type="button" class="btn btn-success btn-xs"
                                                        wire:click="ubahStatus({{ $item->id }}, 'Selesai')"
                                                        wire:confirm="Tandai disposisi ini sebagai selesai?" title="Selesaikan">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                                <button type="button" class="btn btn-danger btn-xs"
                                                    wire:click="hapus({{ $item->id }})"
                                                    wire:confirm="Yakin ingin menghapus disposisi ini?" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <a href="{{ route('superadmin.desposisi.cetak', $item->id) }}"
                                                    class="btn btn-info btn-xs" target="_blank" rel="noopener noreferrer"
                                                    title="Cetak Disposisi">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">
                                                Belum ada data disposisi.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{-- Pagination --}}
                        <div class="mt-3">
                            {{ $disposisis->links() }}
                        </div>
                    </div> {{-- card-body --}}
                </div> {{-- card daftar disposisi --}}
            </div> {{-- container-fluid --}}
        </section>
    </div> {{-- content-wrapper --}}
    {{-- Modal tambah dan edit disposisi --}}
    @include('livewire.superadmin.desposisi.create')
</div> {{-- root Livewire --}}
@script
<script>
    $wire.on('buka-modal-disposisi', () => {
        $('#createModal').modal('show');
    });
    $wire.on('tutup-modal-disposisi', () => {
        $('#createModal').modal('hide');
    });
</script>
@endscript