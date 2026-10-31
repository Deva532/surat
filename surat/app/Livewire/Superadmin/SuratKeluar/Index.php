<?php

namespace App\Livewire\Superadmin\SuratKeluar;

use App\Models\KategoriSurat;
use App\Models\Profil;
use App\Models\SuratKeluar;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Index extends Component
{
    public $kategori_id = '';
    public $penerima = '';
    public $alamat_tujuan = '';
    public $perihal = '';
    public $isi_surat = '';
    public $tanggal_surat = '';
    public $sifat = 'Biasa';


    public function render()
    {
        return view('livewire.superadmin.suratkeluar.index', [
            'kategoriSurat' => KategoriSurat::orderBy('nama_kategori')->get(),
            'suratKeluar' => SuratKeluar::with(['kategori', 'pembuat'])
                ->latest()
                ->get(),
        ]);
    }


    public function mount(): void
    {
        $this->tanggal_surat = now()->toDateString();
    }

    public function simpan(): void
    {
        $this->validate([
            'kategori_id' => [
                'required',
                'exists:kategori_surat,id',
            ],
            'penerima' => 'required|string|max:255',
            'alamat_tujuan' => 'nullable|string|max:2000',
            'perihal' => 'required|string|max:1000',
            'isi_surat' => 'required|string',
            'tanggal_surat' => 'required|date',
            'sifat' => [
                'required',
                Rule::in([
                    'Biasa',
                    'Penting',
                    'Rahasia',
                    'Segera',
                ]),
            ],
        ]);

        DB::transaction(function () {
            // Kunci baris profil selama penomoran berlangsung.
            $profil = Profil::query()
                ->lockForUpdate()
                ->first();

            if (!$profil || blank($profil->kode_lembaga)) {
                throw new \RuntimeException(
                    'Profil instansi atau kode lembaga belum tersedia.'
                );
            }

            $kategori = KategoriSurat::findOrFail(
                $this->kategori_id
            );

            if (blank($kategori->kode_kategori)) {
                throw new \RuntimeException(
                    'Kode kategori surat belum tersedia.'
                );
            }

            $kodeNomenklatur = trim($kategori->kode_kategori);
            $kodeInstansi = strtoupper(trim($profil->kode_lembaga));
            $tahun = (int) date(
                'Y',
                strtotime($this->tanggal_surat)
            );

            $nomorUrut = ((int) SuratKeluar::withTrashed()
                ->where('kode_nomenklatur', $kodeNomenklatur)
                ->where('kode_instansi', $kodeInstansi)
                ->where('tahun', $tahun)
                ->max('nomor_urut')) + 1;

            $nomorSurat = sprintf(
                '%s/%03d/%s/%d',
                $kodeNomenklatur,
                $nomorUrut,
                $kodeInstansi,
                $tahun
            );

            SuratKeluar::create([
                'kode_nomenklatur' => $kodeNomenklatur,
                'nomor_urut' => $nomorUrut,
                'kode_instansi' => $kodeInstansi,
                'tahun' => $tahun,
                'nomor_surat' => $nomorSurat,
                'penerima' => $this->penerima,
                'alamat_tujuan' => $this->alamat_tujuan ?: null,
                'perihal' => $this->perihal,
                'isi_surat' => $this->isi_surat,
                'tanggal_surat' => $this->tanggal_surat,
                'kategori_id' => $kategori->id,
                'file_path' => '',
                'sifat' => $this->sifat,
                'status' => 'Draft',
                'created_by' => auth()->id(),
            ]);
        });

        $this->reset([
            'kategori_id',
            'penerima',
            'alamat_tujuan',
            'perihal',
            'isi_surat',
        ]);

        $this->tanggal_surat = now()->toDateString();
        $this->sifat = 'Biasa';

        session()->flash(
            'success',
            'Surat keluar berhasil disimpan sebagai draft.'
        );
    }
}
