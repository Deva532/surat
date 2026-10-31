<?php

namespace App\Http\Controllers;

use App\Models\KategoriSurat;
use App\Models\Profil;
use App\Models\SuratMasuk;
use App\Models\SuratMasukPenerima;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SuratPublikController extends Controller
{
    /**
     * Daftar 7 Jabatan Resmi Penerima Lembar Disposisi
     */
    public const TUJUH_JABATAN = [
        'Sekretaris Kecamatan',
        'Kepala Seksi Pemerintahan',
        'Kepala Seksi Pelayanan Publik',
        'Kepala Seksi Ketentraman dan Ketertiban Umum',
        'Kepala Seksi Pembangunan dan Pemberdayaan Masyarakat',
        'Kepala Sub Bagian Umum dan Kepegawaian',
        'Kepala Sub Bagian Perencanaan Evaluasi dan Keuangan',
    ];

    public function index()
    {
        $profil = Profil::first();
        $kategoriList = KategoriSurat::orderBy('nama_kategori')->get();
        $jabatanList = self::TUJUH_JABATAN;

        return view('publik.index', compact('profil', 'kategoriList', 'jabatanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pengirim' => ['required', 'string', 'max:255'],
            'instansi_pengirim' => ['nullable', 'string', 'max:255'],
            'kontak_pengirim' => ['nullable', 'string', 'max:50'],
            'nomor_surat' => ['required', 'string', 'max:255'],
            'tanggal_surat' => ['required', 'date'],
            'kategori_id' => ['required', 'integer', 'exists:kategori_surat,id'],
            'perihal' => ['required', 'string', 'max:1000'],
            'ringkasan' => ['nullable', 'string', 'max:3000'],
            'penerima' => ['required', 'array', 'min:1'],
            'penerima.*' => ['required', 'string', Rule::in(self::TUJUH_JABATAN)],
            'file_lampiran' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,jpg,jpeg,png',
                'max:10240', // Maksimal 10 MB
            ],
        ], [
            'nama_pengirim.required' => 'Nama lengkap atau identitas pengirim wajib diisi.',
            'nomor_surat.required' => 'Nomor surat resmi wajib diisi.',
            'tanggal_surat.required' => 'Tanggal surat wajib diisi.',
            'kategori_id.required' => 'Kategori klasifikasi surat wajib dipilih.',
            'perihal.required' => 'Perihal surat wajib diisi.',
            'penerima.required' => 'Pilihlah minimal satu dari 7 jabatan penerima.',
            'penerima.min' => 'Pilihlah minimal satu dari 7 jabatan penerima.',
            'file_lampiran.required' => 'Berkas lampiran surat wajib diunggah.',
            'file_lampiran.mimes' => 'Format berkas harus berupa PDF, DOC, DOCX, JPG, atau PNG.',
            'file_lampiran.max' => 'Ukuran berkas maksimal adalah 10 MB.',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            // 1. Simpan berkas lampiran secara aman
            $file = $request->file('file_lampiran');
            $path = $file->store('suratmasuk', 'public');

            // 2. Tentukan nomor agenda registrasi otomatis
            $kategori = KategoriSurat::findOrFail($validated['kategori_id']);
            $profil = Profil::first();
            $kodeInstansi = $profil->kode_lembaga ?? 'KEC.LMG';
            $tahun = date('Y', strtotime($validated['tanggal_surat']));
            $kodeSurat = $kategori->kode_kategori ?? '400';

            $lastSurat = SuratMasuk::whereYear('created_at', date('Y'))
                ->whereNotNull('nomor_agenda')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            $nextNum = 1;
            if ($lastSurat && $lastSurat->nomor_agenda) {
                $parts = explode('/', $lastSurat->nomor_agenda);
                $nextNum = ((int) ($parts[1] ?? 0)) + 1;
            }

            $nomorAgenda = sprintf('%s/%03d/%s/%d', $kodeSurat, $nextNum, $kodeInstansi, date('Y'));

            // Pengirim gabungan nama & instansi
            $pengirimFinal = !empty($validated['instansi_pengirim'])
                ? "{$validated['nama_pengirim']} ({$validated['instansi_pengirim']})"
                : $validated['nama_pengirim'];

            if (!empty($validated['kontak_pengirim'])) {
                $pengirimFinal .= " [Telp: {$validated['kontak_pengirim']}]";
            }

            $perihalFinal = $validated['perihal'];
            if (!empty($validated['ringkasan'])) {
                $perihalFinal .= "\n\nRingkasan Pengirim:\n" . $validated['ringkasan'];
            }

            // Temukan user admin default untuk created_by
            $adminUser = User::whereIn('role', ['superadmin', 'admin'])->first();
            $createdBy = $adminUser ? $adminUser->id : 2;

            // 3. Simpan data utama surat SATU KALI
            $suratMasuk = SuratMasuk::create([
                'nomor_agenda' => $nomorAgenda,
                'nomor_surat' => $validated['nomor_surat'],
                'pengirim' => $pengirimFinal,
                'penerima_dituju' => implode(', ', $validated['penerima']),
                'perihal' => $perihalFinal,
                'tanggal_surat' => $validated['tanggal_surat'],
                'tanggal_diterima' => now()->toDateString(),
                'kategori_id' => $validated['kategori_id'],
                'file_path' => $path,
                'sifat' => 'Biasa',
                'status' => 'Proses Disposisi',
                'created_by' => $createdBy,
            ]);

            // 4. Hubungkan ke masing-masing penerima dari 7 jabatan
            foreach ($validated['penerima'] as $jabatanName) {
                $user = User::where('jabatan', $jabatanName)->first();
                SuratMasukPenerima::create([
                    'surat_masuk_id' => $suratMasuk->id,
                    'user_id' => $user?->id,
                    'jabatan' => $jabatanName,
                    'status' => 'Terkirim',
                ]);
            }

            return redirect()->route('publik.sukses', ['agenda' => urlencode($suratMasuk->nomor_agenda)])
                ->with('success', 'Surat Anda telah berhasil dikirimkan ke Kecamatan Lamongan.');
        });
    }

    public function sukses(Request $request)
    {
        $agenda = $request->query('agenda');
        $surat = SuratMasuk::where('nomor_agenda', $agenda)->with('penerimaList')->firstOrFail();
        $profil = Profil::first();

        return view('publik.sukses', compact('surat', 'profil'));
    }

    public function lacak(Request $request)
    {
        $keyword = trim($request->get('keyword', ''));
        $profil = Profil::first();
        $hasil = null;

        if (!empty($keyword)) {
            $hasil = SuratMasuk::where('nomor_agenda', $keyword)
                ->orWhere('nomor_surat', $keyword)
                ->with('penerimaList')
                ->first();
        }

        return view('publik.lacak', compact('profil', 'keyword', 'hasil'));
    }
}
