<?php

namespace App\Livewire\Superadmin\Desposisi;

use App\Models\Disposisi;
use App\Models\Profil;
use App\Models\SuratMasuk;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $filterStatus = '';

    public $disposisiId = null;
    public $surat_masuk_id = '';
    public $penerima_id = '';
    public $isi_disposisi = '';
    public $sifat_instruksi = 'Biasa';
    public $batas_waktu = '';
    public $status = 'Belum Dibaca';
    public $catatan_tindak_lanjut = '';

    public $isEdit = false;
    public $showForm = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'surat_masuk_id' => [
                'required',
                'integer',
                'exists:surat_masuks,id',
            ],
            'penerima_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'isi_disposisi' => [
                'required',
                'string',
                'max:5000',
            ],
            'sifat_instruksi' => [
                'required',
                Rule::in(['Biasa', 'Penting', 'Sangat Segera']),
            ],
            'batas_waktu' => [
                'nullable',
                'date',
            ],
            'status' => [
                'required',
                Rule::in(['Belum Dibaca', 'Proses', 'Selesai']),
            ],
            'catatan_tindak_lanjut' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'surat_masuk_id.required' => 'Surat masuk wajib dipilih.',
            'surat_masuk_id.exists' => 'Surat masuk yang dipilih tidak ditemukan.',
            'penerima_id.required' => 'Penerima disposisi wajib dipilih.',
            'penerima_id.exists' => 'Penerima yang dipilih tidak ditemukan.',
            'isi_disposisi.required' => 'Instruksi disposisi wajib diisi.',
            'isi_disposisi.max' => 'Instruksi maksimal 5000 karakter.',
            'sifat_instruksi.required' => 'Sifat instruksi wajib dipilih.',
            'batas_waktu.date' => 'Format batas waktu tidak valid.',
            'status.required' => 'Status disposisi wajib dipilih.',
            'catatan_tindak_lanjut.max' => 'Catatan tindak lanjut maksimal 5000 karakter.',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;

        $this->dispatch('buka-modal-disposisi');
    }

    public function bukaForm(): void
    {
        // Alias jika masih dipakai tombol atau bagian Blade lain.
        $this->create();
    }

    public function tutupForm(): void
    {
        $this->showForm = false;
        $this->dispatch('tutup-modal-disposisi');

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->resetValidation();

        $this->reset([
            'disposisiId',
            'surat_masuk_id',
            'penerima_id',
            'isi_disposisi',
            'batas_waktu',
            'catatan_tindak_lanjut',
        ]);

        $this->sifat_instruksi = 'Biasa';
        $this->status = 'Belum Dibaca';
        $this->isEdit = false;
    }

    public function simpan(): void
    {
        $this->validate();

        abort_unless(Auth::check(), 403, 'Anda harus login.');

        $data = [
            'surat_masuk_id' => $this->surat_masuk_id,
            'pengirim_id' => Auth::id(),
            'penerima_id' => $this->penerima_id,
            'isi_disposisi' => $this->isi_disposisi,
            'sifat_instruksi' => $this->sifat_instruksi,
            'batas_waktu' => $this->batas_waktu ?: null,
            'status' => $this->status,
            'catatan_tindak_lanjut' => $this->catatan_tindak_lanjut ?: null,
        ];

        if ($this->isEdit && $this->disposisiId) {
            $disposisi = Disposisi::findOrFail($this->disposisiId);
            $disposisi->update($data);

            session()->flash('success', 'Disposisi berhasil diperbarui.');
        } else {
            Disposisi::create($data);

            session()->flash('success', 'Disposisi berhasil ditambahkan.');
        }

        $this->tutupForm();
        $this->resetPage();
    }

    public function edit($id): void
    {
        $disposisi = Disposisi::findOrFail($id);

        $this->resetForm();

        $this->disposisiId = $disposisi->id;
        $this->surat_masuk_id = $disposisi->surat_masuk_id;
        $this->penerima_id = $disposisi->penerima_id;
        $this->isi_disposisi = $disposisi->isi_disposisi;
        $this->sifat_instruksi = $disposisi->sifat_instruksi;
        $this->batas_waktu = $disposisi->batas_waktu
            ? $disposisi->batas_waktu->format('Y-m-d')
            : '';
        $this->status = $disposisi->status;
        $this->catatan_tindak_lanjut = $disposisi->catatan_tindak_lanjut ?? '';
        $this->isEdit = true;
        $this->showForm = true;

        $this->dispatch('buka-modal-disposisi');
    }

    public function ubahStatus($id, $statusBaru): void
    {
        $statusValid = ['Belum Dibaca', 'Proses', 'Selesai'];

        if (! in_array($statusBaru, $statusValid, true)) {
            session()->flash('error', 'Status tidak valid.');
            return;
        }

        Disposisi::findOrFail($id)->update([
            'status' => $statusBaru,
        ]);

        session()->flash('success', 'Status disposisi berhasil diubah.');
    }

    public function hapus($id): void
    {
        Disposisi::findOrFail($id)->delete();

        session()->flash('success', 'Disposisi berhasil dihapus.');
    }

    public function render()
    {
        $query = Disposisi::with([
            'suratMasuk',
            'pengirim',
            'penerima',
        ]);

        if (trim($this->search) !== '') {
            $keyword = trim($this->search);

            $query->where(function ($q) use ($keyword) {
                $q->where('isi_disposisi', 'like', "%{$keyword}%")
                    ->orWhere('status', 'like', "%{$keyword}%")
                    ->orWhereHas('suratMasuk', function ($surat) use ($keyword) {
                        $surat->where('nomor_surat', 'like', "%{$keyword}%")
                            ->orWhere('nomor_agenda', 'like', "%{$keyword}%")
                            ->orWhere('perihal', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('penerima', function ($penerima) use ($keyword) {
                        $penerima->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($this->filterStatus !== '') {
            $query->where('status', $this->filterStatus);
        }

        return view('livewire.superadmin.desposisi.index', [
            'disposisis' => $query->latest()->paginate(10),
            'suratMasuks' => SuratMasuk::latest()->get(),
            'penerimas' => User::query()->orderBy('name')->get(),
            'profil' => Profil::first(),
        ]);
    }
}
