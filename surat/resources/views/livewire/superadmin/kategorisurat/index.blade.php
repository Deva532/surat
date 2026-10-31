<div>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>
                            <i class="fas fa-at mr-2"></i>
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
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <button wire:click="create" class="btn btn-sm btn-primary" data-toggle="modal"
                            data-target="#createModal">
                            <i class="fas fa-plus mr-1"></i>
                            Tambah data
                        </button>
                    </div>
                </div>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kategori</th>
                            <th>Kode Kategori</th>
                            <th>Keterangan</th>
                            <th>
                                <i class="fas fa-cog"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dataKategori as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nama_kategori }}</td>
                                <td>{{ $item->kode_kategori }}</td>
                                <td>{{ $item->keterangan ?? '-' }}</td>
                                <td>
                                    <button wire:click="confirmDelete({{ $item->id }})" class="btn btn-danger btn-sm"
                                        data-toggle="modal" data-target="#deleteModal">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">
                                    Belum ada data kategori surat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- /.card-body -->
                <!-- /.card-footer-->
            </div>
            <!-- /.card -->
        </section>
        {{-- create modal --}}
        @include('livewire.superadmin.kategorisurat.create')
        {{-- close modal --}}
        @script
        <script>
            $wire.on('closeModal', () => {
                $('#createModal').modal('hide');
                Swal.fire({
                    title: "Data Berhasil Disimpan",
                    text: "klik tombol ok untuk melanjutkan",
                    icon: "success"
                });
            });
        </script>
        @endscript
        {{-- Delete modal --}}
        @include('livewire.superadmin.kategorisurat.delete')

        @script
        <script>
            $wire.on('closeDeleteModal', () => {
                $('#deleteModal').modal('hide');

                Swal.fire({
                    title: 'Data Berhasil Dihapus',
                    text: 'Klik tombol OK untuk melanjutkan',
                    icon: 'success'
                });
            });
        </script>
        @endscript
    </div>
    {{-- Because she competes with no one, no one can compete with her. --}}
</div>