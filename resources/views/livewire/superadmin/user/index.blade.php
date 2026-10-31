<div>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>
                            <i class="fas fa-user mr-2"></i>
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
                        <div class="btn-group dropleft">
                            <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-print mr-1"></i>
                                Cetak
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item text-danger" href="#" target="_blank">
                                    <i class="fas fa-file-pdf mr-1"></i>
                                    PDF
                                </a>
                                <!-- Dropdown menu links -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>NIP</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>
                                    <i class="fas fa-cog"></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user as $item)
                                    <tr>
                                        <td> {{ $loop->iteration }} </td>
                                        <td> {{ $item->name }} </td>
                                        <td> {{ $item->nip }} </td>
                                        <td> {{ $item->email }} </td>
                                        <td>
                                            @if ($item->role === 'admin')
                                                <span class="badge badge-primary">Guru</span>
                                            @elseif ($item->role === 'superadmin')
                                                <span class="badge badge-info">Admin</span>
                                            @else
                                                <span class="badge badge-secondary">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button wire:click="edit({{ $item->id }})" class="btn btn-sm btn-warning"
                                                data-toggle="modal" data-target="#editModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button wire:click="confrim({{ $item->id }})" class="btn btn-sm btn-danger"
                                                data-toggle="modal" data-target="#deleteModal">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
                <!-- /.card-footer-->
            </div>
            <!-- /.card -->
        </section>
        {{-- create modal --}}
        @include('livewire.superadmin.user.create')
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
        <!-- /.content -->
        {{-- create modal --}}
        @include('livewire.superadmin.user.edit')
        {{-- close modal --}}
        @script
        <script>
            $wire.on('closeEditModal', () => {
                $('#editModal').modal('hide');
                Swal.fire({
                    title: "Data Berhasil Diupdate",
                    text: "klik tombol ok untuk melanjutkan",
                    icon: "success"
                });
            });
        </script>
        @endscript
        {{-- Delete modal --}}
        @include('livewire.superadmin.user.delete')
        {{-- close modal --}}
        @script
        <script>
            $wire.on('closeDeleteModal', () => {
                $('#deleteModal').modal('hide');
                Swal.fire({
                    title: "Data Berhasil Dihapus",
                    text: "klik tombol ok untuk melanjutkan",
                    icon: "success"
                });
            });
        </script>
        @endscript
    </div>
    {{-- Because she competes with no one, no one can compete with her. --}}
</div>