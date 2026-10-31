<?php

namespace App\Livewire\Superadmin\Kategorisurat;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\KategoriSurat;

class Index extends Component
{
    use WithPagination;

    public $kategorisurat_id;
    public $nama_kategori = '';
    public $kode_kategori = '';
    public $keterangan = '';
    public $search = '';


    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function render()
    {
        $dataKategori = KategoriSurat::orderBy('nama_kategori', 'asc')
            ->get();

        return view('livewire.superadmin.kategorisurat.index', [
            'title' => 'Kategori Surat',
            'dataKategori' => $dataKategori,
        ]);
    }


    public function create()
    {
        $this->resetValidation();

        $this->reset([
            'kategorisurat_id',
            'nama_kategori',
            'kode_kategori',
            'keterangan',
        ]);
    }

    public function store()
    {
        $this->validate([
            'nama_kategori' => 'required|string|max:255',
            'kode_kategori' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'kode_kategori.required' => 'Kode kategori wajib diisi.',
        ]);

        KategoriSurat::create([
            'nama_kategori' => $this->nama_kategori,
            'kode_kategori' => $this->kode_kategori,
            'keterangan' => $this->keterangan ?: null,
        ]);

        $this->reset([
            'nama_kategori',
            'kode_kategori',
            'keterangan',
        ]);

        $this->resetValidation();

        $this->dispatch('closeModal');
    }

    public function confirmDelete($id)
    {
        $kategori = KategoriSurat::findOrFail($id);

        $this->kategorisurat_id = $kategori->id;
        $this->nama_kategori = $kategori->nama_kategori;
        $this->kode_kategori = $kategori->kode_kategori;
        $this->keterangan = $kategori->keterangan;
    }

    public function destroy($id)
{
    $kategori = KategoriSurat::findOrFail($id);
    $kategori->delete();

    $this->reset([
        'kategorisurat_id',
        'nama_kategori',
        'kode_kategori',
        'keterangan',
    ]);

    // Khusus untuk menutup modal hapus
    $this->dispatch('closeDeleteModal');
}
}
