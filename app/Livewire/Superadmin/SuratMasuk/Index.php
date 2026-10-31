<?php

namespace App\Livewire\Superadmin\SuratMasuk;

use App\Models\SuratMasuk;
use App\Models\KategoriSurat;
use App\Models\Profil;
use App\Models\Disposisi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;



class Index extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $suratmasuk_id;
    public $nomor_agenda = '';
    public $nomor_surat = '';
    public $pengirim = '';
    public $penerima_dituju = '';
    public $perihal = '';
    public $tanggal_surat = '';
    public $tanggal_diterima = '';
    public $kategori_id = '';
    public $sifat = 'Biasa';
    public $status = 'Diarsipkan';
    public $file;
    public $file_lama;
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function render()
    {
        $suratmasuk = SuratMasuk::with([
            'kategoriSurat',
            'disposisi',
        ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nomor_surat', 'like', '%' . $this->search . '%')
                        ->orWhere('nomor_agenda', 'like', '%' . $this->search . '%')
                        ->orWhere('pengirim', 'like', '%' . $this->search . '%')
                        ->orWhere('perihal', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.superadmin.suratmasuk.index', [
            'title' => 'Surat Masuk',
            'suratmasuk' => $suratmasuk,
            'kategoriSurat' => KategoriSurat::orderBy('nama_kategori')->get(),
        ])->layout('layouts.app');
    }

    public function updatedKategoriId($value)
    {
        if (!$value) {
            $this->nomor_agenda = '';
            return;
        }

        $this->nomor_agenda = $this->generateNomorAgenda($value);
    }
    public function generateNomorAgenda($kategoriId = null)
    {
        $tahun = date('Y');

        $profil = Profil::first();
        $kodeLembaga = $profil->kode_lembaga ?? 'SM';

        // Ambil kode surat berdasarkan kategori yang dipilih
        $kategori = KategoriSurat::find($kategoriId);
        $kodeSurat = $kategori->kode_kategori ?? 'Surat Umum';

        // Cari nomor agenda terakhir pada tahun berjalan
        $lastSurat = SuratMasuk::whereYear('created_at', $tahun)
            ->whereNotNull('nomor_agenda')
            ->latest('id')
            ->first();

        $nomorUrut = 1;

        if ($lastSurat) {
            $parts = explode('/', $lastSurat->nomor_agenda);

            // Format baru: KODE-SURAT/NOMOR-URUT/KODE-LEMBAGA/TAHUN
            $nomorUrut = ((int) ($parts[1] ?? 0)) + 1;
        }

        return $kodeSurat
            . '/' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT)
            . '/' . $kodeLembaga
            . '/' . $tahun;
    }

    public function create()
    {
        $this->resetValidation();

        $this->reset([
            'suratmasuk_id',
            'nomor_surat',
            'pengirim',
            'penerima_dituju',
            'perihal',
            'tanggal_surat',
            'tanggal_diterima',
            'kategori_id',
            'file',
            'file_lama',
        ]);

        $this->nomor_agenda = $this->generateNomorAgenda();
        $this->sifat = 'Biasa';
        $this->status = 'Diarsipkan';
    }

    protected function rules()
    {
        return [
            'nomor_agenda' => [
                'required',
                Rule::unique('surat_masuks', 'nomor_agenda')
                    ->ignore($this->suratmasuk_id),
            ],
            'nomor_surat' => [
                'required',
                Rule::unique('surat_masuks', 'nomor_surat')
                    ->ignore($this->suratmasuk_id),
            ],
            'pengirim' => 'required|string|max:255',
            'penerima_dituju' => 'nullable|string|max:255',
            'perihal' => 'required|string',
            'tanggal_surat' => 'required|date',
            'tanggal_diterima' => 'required|date',
            'kategori_id' => 'required|exists:kategori_surat,id',
            'sifat' => 'required|in:Biasa,Penting,Rahasia,Segera',
            'status' => 'required|in:Diarsipkan,Proses Disposisi,Selesai',
            'file' => $this->suratmasuk_id
                ? 'nullable|file|mimes:pdf|max:2048'
                : 'required|file|mimes:pdf|max:2048',
        ];
    }

    protected function messages()
    {
        return [
            'nomor_agenda.required' => 'Nomor agenda wajib diisi.',
            'nomor_agenda.unique' => 'Nomor agenda sudah digunakan.',
            'nomor_surat.required' => 'Nomor surat wajib diisi.',
            'nomor_surat.unique' => 'Nomor surat sudah digunakan.',
            'pengirim.required' => 'Pengirim wajib diisi.',
            'perihal.required' => 'Perihal wajib diisi.',
            'tanggal_surat.required' => 'Tanggal surat wajib diisi.',
            'tanggal_diterima.required' => 'Tanggal diterima wajib diisi.',
            'kategori_id.required' => 'Kategori surat wajib dipilih.',
            'kategori_id.exists' => 'Kategori surat tidak valid.',
            'file.required' => 'File surat wajib diunggah.',
            'file.mimes' => 'File surat harus berformat PDF.',
            'file.max' => 'Ukuran file maksimal 2 MB.',
        ];
    }

    public function store()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Silakan login terlebih dahulu.');
            return;
        }

        $this->validate([
            'nomor_agenda' => 'required|string|unique:surat_masuks,nomor_agenda',
            'nomor_surat' => 'required|string|max:255',
            'pengirim' => 'required|string|max:255',
            'penerima_dituju' => 'nullable|string|max:255',
            'perihal' => 'required|string',
            'tanggal_surat' => 'required|date',
            'tanggal_diterima' => 'required|date',
            'kategori_id' => 'required|exists:kategori_surat,id',
            'sifat' => 'required|in:Biasa,Penting,Rahasia,Segera',
            'status' => 'required|in:Diarsipkan,Proses Disposisi,Selesai',
            'file' => 'required|file|mimes:pdf|max:2048',
        ]);

        SuratMasuk::create([
            'nomor_agenda' => $this->nomor_agenda,
            'nomor_surat' => $this->nomor_surat,
            'pengirim' => $this->pengirim,
            'penerima_dituju' => $this->penerima_dituju,
            'perihal' => $this->perihal,
            'tanggal_surat' => $this->tanggal_surat,
            'tanggal_diterima' => $this->tanggal_diterima,
            'kategori_id' => $this->kategori_id,
            'file_path' => $this->file->store('suratmasuk', 'public'),
            'sifat' => $this->sifat,
            'status' => $this->status,
            'created_by' => Auth::id(),
        ]);

        $this->reset([
            'nomor_agenda',
            'nomor_surat',
            'pengirim',
            'penerima_dituju',
            'perihal',
            'tanggal_surat',
            'tanggal_diterima',
            'kategori_id',
            'file',
        ]);

        $this->dispatch('closeModal');
        session()->flash('success', 'Surat masuk berhasil disimpan.');
    }

    public function edit($id)
    {
        $this->resetValidation();

        $surat = SuratMasuk::findOrFail($id);

        $this->suratmasuk_id = $surat->id;
        $this->nomor_agenda = $surat->nomor_agenda;
        $this->nomor_surat = $surat->nomor_surat;
        $this->pengirim = $surat->pengirim;
        $this->penerima_dituju = $surat->penerima_dituju;
        $this->perihal = $surat->perihal;
        $this->tanggal_surat = $surat->tanggal_surat ? Carbon::parse($surat->tanggal_surat)->format('Y-m-d') : '';
        $this->tanggal_diterima = $surat->tanggal_diterima ? Carbon::parse($surat->tanggal_diterima)->format('Y-m-d') : '';
        $this->kategori_id = $surat->kategori_id;
        $this->sifat = $surat->sifat;
        $this->status = $surat->status;
        $this->file = null;
        $this->file_lama = $surat->file_path;
    }

    public function update()
    {
        $this->validate();

        $surat = SuratMasuk::findOrFail($this->suratmasuk_id);

        $path = $surat->file_path;

        if ($this->file) {
            $path = $this->file->store('suratmasuk', 'public');
            Storage::disk('public')->delete($surat->file_path);
        }

        $surat->update([
            'nomor_agenda' => $this->nomor_agenda,
            'nomor_surat' => $this->nomor_surat,
            'pengirim' => $this->pengirim,
            'penerima_dituju' => $this->penerima_dituju ?: null,
            'perihal' => $this->perihal,
            'tanggal_surat' => $this->tanggal_surat,
            'tanggal_diterima' => $this->tanggal_diterima,
            'kategori_id' => $this->kategori_id,
            'file_path' => $path,
            'sifat' => $this->sifat,
            'status' => $this->status,
        ]);

        $this->reset([
            'suratmasuk_id',
            'nomor_agenda',
            'nomor_surat',
            'pengirim',
            'penerima_dituju',
            'perihal',
            'tanggal_surat',
            'tanggal_diterima',
            'kategori_id',
            'file',
            'file_lama',
        ]);

        $this->resetValidation();
        $this->dispatch('closeEditModal');
    }

    public function confirmDelete($id)
    {
        $surat = SuratMasuk::findOrFail($id);

        $this->suratmasuk_id = $surat->id;
        $this->nomor_agenda = $surat->nomor_agenda;
        $this->nomor_surat = $surat->nomor_surat;
        $this->pengirim = $surat->pengirim;
        $this->perihal = $surat->perihal;
        $this->file_lama = $surat->file_path;
    }

    public function ubahStatus($id, $statusBaru)
    {
        // Pastikan status yang dikirim valid
        if (!in_array($statusBaru, [
            'Diarsipkan',
            'Proses Disposisi',
            'Selesai',
        ])) {
            session()->flash('error', 'Status tidak valid.');
            return;
        }

        $surat = SuratMasuk::findOrFail($id);

        // Perbarui status surat
        $surat->update([
            'status' => $statusBaru,
        ]);

        session()->flash(
            'success',
            'Status surat berhasil diubah menjadi ' . $statusBaru . '.'
        );
    }

    public function destroy($id)
    {
        $surat = SuratMasuk::findOrFail($id);

        if ($surat->file_path) {
            Storage::disk('public')->delete($surat->file_path);
        }

        $surat->delete();

        $this->reset([
            'suratmasuk_id',
            'nomor_agenda',
            'nomor_surat',
            'pengirim',
            'perihal',
            'file_lama',
        ]);

        $this->dispatch('closeDeleteModal');
    }
}
