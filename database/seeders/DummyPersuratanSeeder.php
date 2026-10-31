<?php

namespace Database\Seeders;

use App\Models\Disposisi;
use App\Models\DisposisiPenerima;
use App\Models\KategoriSurat;
use App\Models\Profil;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\SuratMasukPenerima;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DummyPersuratanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * MENGISI SELURUH DATA DUMMY PERSURATAN TANPA MENGUBAH / MEMBUAT USER BARU.
     */
    public function run(): void
    {
        // 1. Ambil referensi akun pengguna yang sudah ada
        $adminUser = User::whereIn('role', ['superadmin', 'admin'])->first() ?? User::first();
        $adminId = $adminUser ? $adminUser->id : 1;

        $usersByJabatan = User::whereNotNull('jabatan')->get()->keyBy('jabatan');

        $tujuhJabatan = [
            'Sekretaris Kecamatan',
            'Kepala Seksi Pemerintahan',
            'Kepala Seksi Pelayanan Publik',
            'Kepala Seksi Ketentraman dan Ketertiban Umum',
            'Kepala Seksi Pembangunan dan Pemberdayaan Masyarakat',
            'Kepala Sub Bagian Umum dan Kepegawaian',
            'Kepala Sub Bagian Perencanaan Evaluasi dan Keuangan',
        ];

        // 2. Siapkan Berkas Sample Dummy jika belum ada
        $sampleDir = storage_path('app/public/suratmasuk');
        if (!File::exists($sampleDir)) {
            File::makeDirectory($sampleDir, 0755, true);
        }
        $sampleFilePath = 'suratmasuk/sample_dokumen_resmi.pdf';
        $sampleFullDisk = storage_path('app/public/' . $sampleFilePath);
        if (!File::exists($sampleFullDisk)) {
            // Gunakan file PDF yang sudah ada atau buat file dummy text
            $existingPdf = File::glob($sampleDir . '/*.pdf');
            if (!empty($existingPdf)) {
                File::copy($existingPdf[0], $sampleFullDisk);
            } else {
                File::put($sampleFullDisk, "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj 3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000052 00000 n\n0000000101 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n185\n%%EOF");
            }
        }

        // =========================================================================
        // 3. SEED PROFIL INSTANSI KECAMATAN LAMONGAN
        // =========================================================================
        $profil = Profil::firstOrNew([]);
        $profil->instansi = 'Pemerintah Kabupaten Lamongan';
        $profil->dinas = 'Kecamatan Lamongan';
        $profil->nama_sekolah = 'Kantor Kecamatan Lamongan';
        $profil->kode_lembaga = 'KEC.LMG';
        $profil->alamat = 'Jl. Basuki Rahmat No. 01, Kelurahan Sidokumpul';
        $profil->kecamatan = 'Lamongan';
        $profil->kabupaten = 'Lamongan';
        $profil->telepon = '(0322) 321155';
        $profil->email = 'kecamatan.lamongan@lamongankab.go.id';
        $profil->website = 'https://lamongankab.go.id/kecamatan-lamongan';
        $profil->kepala_sekolah = 'H. Ahmad Fauzi, S.STP., M.Si';
        $profil->nip_kepala_sekolah = '197508141994121001';
        $profil->pangkat_pimpinan = 'Pembina Tk. I (IV/b)';
        $profil->status = 'Definitif';
        $profil->save();

        // =========================================================================
        // 4. SEED KATEGORI SURAT (KLASIFIKASI KEARSIPAN KEDINASAN)
        // =========================================================================
        $daftarKategori = [
            [
                'kode_kategori' => '000',
                'nama_kategori' => 'Umum & Tata Usaha',
                'keterangan'    => 'Meliputi surat edaran umum, undangan rapat dinas, dan urusan ketatausahaan instansi.',
            ],
            [
                'kode_kategori' => '100',
                'nama_kategori' => 'Pemerintahan & Otonomi Daerah',
                'keterangan'    => 'Meliputi urusan ketenteraman, ketertiban umum, penegakan perda, dan tata kelola desa/kelurahan.',
            ],
            [
                'kode_kategori' => '400',
                'nama_kategori' => 'Kesejahteraan Rakyat & Pelayanan Publik',
                'keterangan'    => 'Meliputi layanan administrasi kependudukan (KTP/KK), jaminan sosial, bansos, dan kesehatan masyarakat.',
            ],
            [
                'kode_kategori' => '500',
                'nama_kategori' => 'Perekonomian & Pemberdayaan Masyarakat',
                'keterangan'    => 'Meliputi pembangunan pedesaan, pemberdayaan ekonomi warga, UMKM, pertanian, dan bantuan infrastruktur desa.',
            ],
            [
                'kode_kategori' => '800',
                'nama_kategori' => 'Kepegawaian & Aparatur Sipil',
                'keterangan'    => 'Meliputi kenaikan pangkat, mutasi, penilaian kinerja, SKP, cuti pegawai, dan pembinaan ASN.',
            ],
            [
                'kode_kategori' => '900',
                'nama_kategori' => 'Keuangan & Pengelolaan Aset',
                'keterangan'    => 'Meliputi evaluasi APBDes, Dana Desa (DD), Alokasi Dana Desa (ADD), pelaporan SPJ, dan anggaran operasional.',
            ],
        ];

        $kategoriMap = [];
        foreach ($daftarKategori as $kat) {
            $recordKat = KategoriSurat::updateOrCreate(
                ['kode_kategori' => $kat['kode_kategori']],
                [
                    'nama_kategori' => $kat['nama_kategori'],
                    'keterangan'    => $kat['keterangan'],
                ]
            );
            $kategoriMap[$kat['kode_kategori']] = $recordKat->id;
        }

        // =========================================================================
        // 5. SEED SURAT MASUK, ROUTING PENERIMA, & LEMBAR DISPOSISI
        // =========================================================================
        $dummySuratMasuk = [
            [
                'nomor_surat'     => '005/1420/413.011/2026',
                'pengirim'        => 'Sekretariat Daerah Kabupaten Lamongan',
                'kode_kategori'   => '000',
                'perihal'         => 'Undangan Rapat Koordinasi Pelaksanaan Upacara Peringatan Hari Jadi Lamongan ke-457',
                'sifat'           => 'Penting',
                'status'          => 'Proses Disposisi',
                'hari_lalu'       => 2,
                'penerima_target' => [
                    'Sekretaris Kecamatan',
                    'Kepala Sub Bagian Umum dan Kepegawaian',
                    'Kepala Seksi Ketentraman dan Ketertiban Umum',
                ],
                'disposisi' => [
                    'sifat_instruksi'   => 'Penting',
                    'instruksi'         => ['Proses Lebih Lanjut', 'Koordinasi/Konfirmasikan'],
                    'instruksi_lainnya' => null,
                    'isi_disposisi'     => 'Fasilitasi jadwal gladi dan koordinasikan kesiapan personil upacara kontingen Kecamatan Lamongan.',
                ],
            ],
            [
                'nomor_surat'     => '470/825/413.102/2026',
                'pengirim'        => 'Dinas Kependudukan dan Pencatatan Sipil Kab. Lamongan',
                'kode_kategori'   => '400',
                'perihal'         => 'Pemberitahuan Jadwal Pelayanan Perekaman KTP-el Keliling Jemput Bola di Wilayah Kecamatan Lamongan',
                'sifat'           => 'Segera',
                'status'          => 'Proses Disposisi',
                'hari_lalu'       => 3,
                'penerima_target' => [
                    'Kepala Seksi Pelayanan Publik',
                    'Sekretaris Kecamatan',
                ],
                'disposisi' => [
                    'sifat_instruksi'   => 'Sangat Segera',
                    'instruksi'         => ['Tanggapan dan Saran', 'Proses Lebih Lanjut', 'Koordinasi/Konfirmasikan'],
                    'instruksi_lainnya' => null,
                    'isi_disposisi'     => 'Sosialisasikan jadwal ke seluruh Kepala Desa dan Lurah agar warga lansia dan pemula segera mendaftar.',
                ],
            ],
            [
                'nomor_surat'     => '300/412/413.115/2026',
                'pengirim'        => 'Satuan Polisi Pamong Praja (Satpol PP) Kab. Lamongan',
                'kode_kategori'   => '100',
                'perihal'         => 'Permohonan Bantuan Personil Operasi Gabungan Penertiban PKL & Reklame Tanpa Izin di Jalan Protokol',
                'sifat'           => 'Penting',
                'status'          => 'Selesai',
                'hari_lalu'       => 5,
                'penerima_target' => [
                    'Kepala Seksi Ketentraman dan Ketertiban Umum',
                ],
                'disposisi' => [
                    'sifat_instruksi'   => 'Penting',
                    'instruksi'         => ['Proses Lebih Lanjut', 'Koordinasi/Konfirmasikan'],
                    'instruksi_lainnya' => null,
                    'isi_disposisi'     => 'Tugaskan personil Trantib Kecamatan bergabung dengan Satpol PP Kabupaten untuk operasi tertib trotoar.',
                ],
            ],
            [
                'nomor_surat'     => '411.2/310/413.108/2026',
                'pengirim'        => 'Dinas Pemberdayaan Masyarakat dan Desa (DPMD)',
                'kode_kategori'   => '500',
                'perihal'         => 'Verifikasi dan Monev Penyaluran Bantuan Langsung Tunai Dana Desa (BLT-DD) Triwulan I TA 2026',
                'sifat'           => 'Biasa',
                'status'          => 'Proses Disposisi',
                'hari_lalu'       => 6,
                'penerima_target' => [
                    'Kepala Seksi Pembangunan dan Pemberdayaan Masyarakat',
                    'Kepala Sub Bagian Perencanaan Evaluasi dan Keuangan',
                ],
                'disposisi' => [
                    'sifat_instruksi'   => 'Biasa',
                    'instruksi'         => ['Proses Lebih Lanjut'],
                    'instruksi_lainnya' => null,
                    'isi_disposisi'     => 'Lakukan monitoring sampling ke desa penerima dan laporkan berita acara rekapitulasi realisasi.',
                ],
            ],
            [
                'nomor_surat'     => '900/188/Desa.MD/2026',
                'pengirim'        => 'Pemerintah Desa Made',
                'kode_kategori'   => '900',
                'perihal'         => 'Pengajuan Rekomendasi Pencairan Alokasi Dana Desa (ADD) dan Dana Bagi Hasil Pajak Tahap I',
                'sifat'           => 'Biasa',
                'status'          => 'Proses Disposisi',
                'hari_lalu'       => 7,
                'penerima_target' => [
                    'Kepala Seksi Pemerintahan',
                    'Kepala Sub Bagian Perencanaan Evaluasi dan Keuangan',
                ],
                'disposisi' => [
                    'sifat_instruksi'   => 'Biasa',
                    'instruksi'         => ['Tanggapan dan Saran', 'Proses Lebih Lanjut'],
                    'instruksi_lainnya' => null,
                    'isi_disposisi'     => 'Cek kelengkapan berkas administrasi dan SPJ pertanggungjawaban sebelum draft rekomendasi ditandatangani.',
                ],
            ],
            [
                'nomor_surat'     => '800/512/413.205/2026',
                'pengirim'        => 'Badan Kepegawaian dan Pengembangan SDM (BKPSDM)',
                'kode_kategori'   => '800',
                'perihal'         => 'Penyampaian Surat Keputusan Kenaikan Pangkat ASN Periode April 2026 di Lingkungan Kantor Kecamatan',
                'sifat'           => 'Penting',
                'status'          => 'Selesai',
                'hari_lalu'       => 10,
                'penerima_target' => [
                    'Kepala Sub Bagian Umum dan Kepegawaian',
                    'Sekretaris Kecamatan',
                ],
                'disposisi' => [
                    'sifat_instruksi'   => 'Biasa',
                    'instruksi'         => ['Proses Lebih Lanjut'],
                    'instruksi_lainnya' => null,
                    'isi_disposisi'     => 'Serahkan petikan SK kepada ASN yang bersangkutan dan perbarui data file kepegawaian internal.',
                ],
            ],
            [
                'nomor_surat'     => '050/789/413.101/2026',
                'pengirim'        => 'Badan Perencanaan Pembangunan Daerah (Bappeda)',
                'kode_kategori'   => '500',
                'perihal'         => 'Penyampaian Jadwal Musyawarah Perencanaan Pembangunan (Musrenbang) RKPD Tingkat Kecamatan Lamongan',
                'sifat'           => 'Penting',
                'status'          => 'Proses Disposisi',
                'hari_lalu'       => 12,
                'penerima_target' => [
                    'Sekretaris Kecamatan',
                    'Kepala Seksi Pembangunan dan Pemberdayaan Masyarakat',
                    'Kepala Sub Bagian Perencanaan Evaluasi dan Keuangan',
                ],
                'disposisi' => [
                    'sifat_instruksi'   => 'Penting',
                    'instruksi'         => ['Proses Lebih Lanjut', 'Koordinasi/Konfirmasikan'],
                    'instruksi_lainnya' => null,
                    'isi_disposisi'     => 'Siapkan tempat aula, sound system, bahan paparan usulan prioritas desa, dan undangan seluruh delegasi.',
                ],
            ],
            [
                'nomor_surat'     => 'B/128/IV/2026/Sek.Lmg',
                'pengirim'        => 'Kepolisian Sektor Lamongan Kota',
                'kode_kategori'   => '100',
                'perihal'         => 'Pemberitahuan Rencana Pengamanan Bersama Ibadah Hari Besar Keagamaan di Wilayah Kecamatan Lamongan',
                'sifat'           => 'Penting',
                'status'          => 'Selesai',
                'hari_lalu'       => 15,
                'penerima_target' => [
                    'Kepala Seksi Ketentraman dan Ketertiban Umum',
                ],
                'disposisi' => [
                    'sifat_instruksi'   => 'Penting',
                    'instruksi'         => ['Koordinasi/Konfirmasikan', 'Proses Lebih Lanjut'],
                    'instruksi_lainnya' => null,
                    'isi_disposisi'     => 'Siapkan regu Trantib untuk patroli sinergitas bersama Babinkamtibmas dan Babinsa di titik-titik rumah ibadah.',
                ],
            ],
            [
                'nomor_surat'     => '440/215/Pusk.Lmg/2026',
                'pengirim'        => 'Puskesmas Lamongan Kota',
                'kode_kategori'   => '400',
                'perihal'         => 'Laporan Rekapitulasi Pelaksanaan Intervensi Serentak Pencegahan Stunting dan Imunisasi Posyandu',
                'sifat'           => 'Biasa',
                'status'          => 'Diarsipkan',
                'hari_lalu'       => 18,
                'penerima_target' => [
                    'Kepala Seksi Pelayanan Publik',
                    'Kepala Seksi Pembangunan dan Pemberdayaan Masyarakat',
                ],
                'disposisi' => null, // Contoh surat yang langsung diarsipkan
            ],
            [
                'nomor_surat'     => '140/94/Kel.SDK/2026',
                'pengirim'        => 'Pemerintah Kelurahan Sidokumpul',
                'kode_kategori'   => '100',
                'perihal'         => 'Laporan Hasil Pemilihan Ketua Rukun Warga (RW) 04 Periode 2026-2031 dan Permohonan Pengukuhan',
                'sifat'           => 'Biasa',
                'status'          => 'Proses Disposisi',
                'hari_lalu'       => 20,
                'penerima_target' => [
                    'Kepala Seksi Pemerintahan',
                ],
                'disposisi' => [
                    'sifat_instruksi'   => 'Biasa',
                    'instruksi'         => ['Tanggapan dan Saran', 'Proses Lebih Lanjut'],
                    'instruksi_lainnya' => null,
                    'isi_disposisi'     => 'Teliti berita acara musyawarah warga dan siapkan draft Keputusan Camat tentang pengesahan pengurus RW.',
                ],
            ],
            [
                'nomor_surat'     => '270/145/KPU-Kab/2026',
                'pengirim'        => 'Komisi Pemilihan Umum (KPU) Kabupaten Lamongan',
                'kode_kategori'   => '100',
                'perihal'         => 'Permohonan Fasilitasi Ruang Sekretariat dan Tenaga Pendukung Panitia Pemilihan Kecamatan (PPK)',
                'sifat'           => 'Penting',
                'status'          => 'Proses Disposisi',
                'hari_lalu'       => 22,
                'penerima_target' => [
                    'Sekretaris Kecamatan',
                    'Kepala Sub Bagian Umum dan Kepegawaian',
                ],
                'disposisi' => [
                    'sifat_instruksi'   => 'Penting',
                    'instruksi'         => ['Proses Lebih Lanjut', 'Koordinasi/Konfirmasikan'],
                    'instruksi_lainnya' => null,
                    'isi_disposisi'     => 'Sediakan ruang rapat samping barat dan tunjuk staf pelaksana untuk membantu urusan administrasi PPK.',
                ],
            ],
            [
                'nomor_surat'     => '660/328/413.111/2026',
                'pengirim'        => 'Dinas Lingkungan Hidup Kab. Lamongan',
                'kode_kategori'   => '500',
                'perihal'         => 'Gerakan Aksi Bersih Saluran Air dan Sungai Perkotaan Menghadapi Puncak Musim Hujan',
                'sifat'           => 'Biasa',
                'status'          => 'Selesai',
                'hari_lalu'       => 25,
                'penerima_target' => [
                    'Kepala Seksi Ketentraman dan Ketertiban Umum',
                    'Kepala Seksi Pembangunan dan Pemberdayaan Masyarakat',
                ],
                'disposisi' => [
                    'sifat_instruksi'   => 'Biasa',
                    'instruksi'         => ['Proses Lebih Lanjut', 'Koordinasi/Konfirmasikan'],
                    'instruksi_lainnya' => null,
                    'isi_disposisi'     => 'Ajak partisipasi warga di bantaran kali melalui lurah setempat dan siapkan armada pengangkut sampah.',
                ],
            ],
        ];

        $nomorUrutAgenda = 1;
        $tahunSekarang = date('Y');

        foreach ($dummySuratMasuk as $index => $item) {
            $kategoriId = $kategoriMap[$item['kode_kategori']] ?? KategoriSurat::first()?->id;
            $kodeKat = $item['kode_kategori'];
            $nomorAgenda = sprintf('%s/%03d/KEC.LMG/%d', $kodeKat, $nomorUrutAgenda++, $tahunSekarang);

            $tglSurat = Carbon::now()->subDays($item['hari_lalu']);
            $tglDiterima = (clone $tglSurat)->addDay();

            // Cek apakah surat dengan nomor surat ini sudah ada
            $suratMasuk = SuratMasuk::updateOrCreate(
                ['nomor_surat' => $item['nomor_surat']],
                [
                    'nomor_agenda'    => $nomorAgenda,
                    'pengirim'        => $item['pengirim'],
                    'penerima_dituju' => implode(', ', $item['penerima_target']),
                    'perihal'         => $item['perihal'],
                    'tanggal_surat'   => $tglSurat->toDateString(),
                    'tanggal_diterima'=> $tglDiterima->toDateString(),
                    'kategori_id'     => $kategoriId,
                    'file_path'       => $sampleFilePath,
                    'sifat'           => $item['sifat'],
                    'status'          => $item['status'],
                    'created_by'      => $adminId,
                ]
            );

            // Routing Penerima Surat
            foreach ($item['penerima_target'] as $jabatanName) {
                $userPenerima = $usersByJabatan->get($jabatanName);
                $statusPenerima = match ($item['status']) {
                    'Selesai'          => 'Selesai',
                    'Proses Disposisi' => ($index % 2 === 0 ? 'Diproses' : 'Dibaca'),
                    default            => 'Terkirim',
                };

                SuratMasukPenerima::updateOrCreate(
                    [
                        'surat_masuk_id' => $suratMasuk->id,
                        'jabatan'        => $jabatanName,
                    ],
                    [
                        'user_id' => $userPenerima?->id,
                        'status'  => $statusPenerima,
                        'catatan' => $statusPenerima === 'Selesai'
                            ? 'Instruksi telah selesai dikoordinasikan dan ditindaklanjuti secara teknis di lapangan.'
                            : ($statusPenerima === 'Diproses' ? 'Sedang dalam penyiapan bahan administrasi dan konfirmasi pihak terkait.' : 'Telah dibaca oleh pejabat penerima.'),
                    ]
                );
            }

            // Lembar Disposisi jika ada data disposisi
            if (!empty($item['disposisi'])) {
                $dispConfig = $item['disposisi'];
                $tglDisp = (clone $tglDiterima);
                $batasWaktu = (clone $tglDisp)->addDays(5)->toDateString();

                $disposisi = Disposisi::updateOrCreate(
                    [
                        'surat_masuk_id' => $suratMasuk->id,
                    ],
                    [
                        'pengirim_id'           => $adminId,
                        'penerima_id'           => null,
                        'tanggal_disposisi'     => $tglDisp->toDateString(),
                        'penerima_jabatan'      => $item['penerima_target'],
                        'isi_disposisi'         => $dispConfig['isi_disposisi'],
                        'instruksi'             => $dispConfig['instruksi'],
                        'instruksi_lainnya'     => $dispConfig['instruksi_lainnya'],
                        'sifat_instruksi'       => $dispConfig['sifat_instruksi'],
                        'batas_waktu'           => $batasWaktu,
                        'status'                => $item['status'] === 'Selesai' ? 'Selesai' : 'Proses',
                        'catatan_tindak_lanjut' => $item['status'] === 'Selesai' ? 'Selesai ditindaklanjuti seluruh seksi terkait.' : null,
                    ]
                );

                // Tracking per jabatan di tabel disposisi_penerima
                foreach ($item['penerima_target'] as $jabatanName) {
                    $userPenerima = $usersByJabatan->get($jabatanName);
                    $statusTracking = $item['status'] === 'Selesai' ? 'Selesai' : 'Proses';

                    DisposisiPenerima::updateOrCreate(
                        [
                            'disposisi_id' => $disposisi->id,
                            'jabatan'      => $jabatanName,
                        ],
                        [
                            'user_id'               => $userPenerima?->id,
                            'status'                => $statusTracking,
                            'catatan_tindak_lanjut' => $statusTracking === 'Selesai' ? 'Laporan hasil telah disampaikan kepada pimpinan.' : 'Dalam proses koordinasi staf.',
                            'dibaca_pada'           => (clone $tglDisp)->addHours(2),
                            'selesai_pada'          => $statusTracking === 'Selesai' ? (clone $tglDisp)->addDays(3) : null,
                        ]
                    );
                }
            }
        }

        // =========================================================================
        // 6. SEED SURAT KELUAR RESMI KECAMATAN LAMONGAN
        // =========================================================================
        $dummySuratKeluar = [
            [
                'kode_nomenklatur' => '005',
                'nomor_urut'       => 1,
                'penerima'         => 'Kepala Desa dan Lurah se-Kecamatan Lamongan',
                'alamat_tujuan'    => 'Di Tempat Masing-masing',
                'kode_kategori'    => '000',
                'perihal'          => 'Undangan Rapat Koordinasi Rutin Forkopimcam dan Evaluasi Kinerja Penyelenggaraan Pemerintahan Bulan Berjalan',
                'isi_surat'        => "Dalam rangka sinkronisasi kebijakan dan peningkatan pelayanan publik di wilayah Kecamatan Lamongan, bersama ini kami mengharap kehadiran Saudara pada:\n\nHari / Tanggal : Senin, 10 Maret 2026\nWaktu : 09.00 WIB s/d Selesai\nTempat : Pendopo Kecamatan Lamongan\nAcara : Rapat Koordinasi dan Evaluasi Ketenteraman Wilayah.\n\nMengingat pentingnya acara tersebut, dimohon hadir tepat waktu dan tidak mewakilkan.",
                'sifat'            => 'Penting',
                'status'           => 'Terkirim',
                'hari_lalu'        => 3,
            ],
            [
                'kode_nomenklatur' => '470',
                'nomor_urut'       => 2,
                'penerima'         => 'Kepala Dinas Kependudukan dan Pencatatan Sipil Kab. Lamongan',
                'alamat_tujuan'    => 'Jl. Veteran No. 12, Lamongan',
                'kode_kategori'    => '400',
                'perihal'          => 'Permohonan Penambahan Blanko KTP Elektronik dan Jadwal Layanan Mobile Jemput Bola Lansia',
                'isi_surat'        => "Sehubungan dengan meningkatnya permohonan penerbitan KTP Elektronik bagi warga pemula usia 17 tahun dan warga lansia berkebutuhan khusus di wilayah Kecamatan Lamongan, dengan ini kami sampaikan permohonan dukungan:\n\n1. Penambahan alokasi kuota blanko KTP-el sebanyak 500 keping.\n2. Jadwal fasilitasi mobil perekaman keliling ke 3 desa prioritas.\n\nDemikian atas perhatian dan kerjasamanya disampaikan terima kasih.",
                'sifat'            => 'Biasa',
                'status'           => 'Disetujui',
                'hari_lalu'        => 7,
            ],
            [
                'kode_nomenklatur' => '140',
                'nomor_urut'       => 3,
                'penerima'         => 'Ketua RW 04 Kelurahan Sidokumpul',
                'alamat_tujuan'    => 'Kelurahan Sidokumpul, Lamongan',
                'kode_kategori'    => '100',
                'perihal'          => 'Penyampaian Surat Keputusan Camat Lamongan tentang Pengesahan Pengurus Rukun Warga (RW) 04 Masa Bakti 2026-2031',
                'isi_surat'        => "Bersama ini kami sampaikan petikan Surat Keputusan Camat Lamongan Nomor 140/08/Kep/413.501/2026 tentang Pengesahan dan Pengukuhan Pengurus Rukun Warga (RW) 04 Kelurahan Sidokumpul. Selamat bertugas dalam melayani masyarakat dan menjaga kerukunan warga.",
                'sifat'            => 'Biasa',
                'status'           => 'Terkirim',
                'hari_lalu'        => 9,
            ],
            [
                'kode_nomenklatur' => '300',
                'nomor_urut'       => 4,
                'penerima'         => 'Kapolsek Lamongan Kota dan Danramil 0812/01 Lamongan',
                'alamat_tujuan'    => 'Jl. Pahlawan, Lamongan',
                'kode_kategori'    => '100',
                'perihal'          => 'Permohonan Bantuan Pengamanan Jalur Pawai Budaya dan Kirab Santri Tingkat Pelajar',
                'sifat'            => 'Penting',
                'status'           => 'Disetujui',
                'hari_lalu'        => 14,
                'isi_surat'        => "Diberitahukan bahwa pada hari Sabtu mendatang akan diadakan pawai kebudayaan santri yang melintasi jalan protokol dalam kota Lamongan. Demi kelancaran lalu lintas dan kamtibmas, mohon kiranya dapat menugaskan personil pengamanan gabungan.",
            ],
            [
                'kode_nomenklatur' => '900',
                'nomor_urut'       => 5,
                'penerima'         => 'Kepala Badan Pengelolaan Keuangan dan Aset Daerah (BPKAD) Kab. Lamongan',
                'alamat_tujuan'    => 'Sekretariat Daerah Lamongan',
                'kode_kategori'    => '900',
                'perihal'          => 'Penyampaian Laporan Pertanggungjawaban Realisasi Anggaran Operasional Kecamatan Triwulan I TA 2026',
                'isi_surat'        => "Melalui surat ini kami sampaikan berkas Laporan Pertanggungjawaban (SPJ) Realisasi Anggaran Triwulan I Tahun Anggaran 2026 Kantor Kecamatan Lamongan beserta buku kas umum dan bukti transaksi pendukung sebagaimana terlampir.",
                'sifat'            => 'Biasa',
                'status'           => 'Draft',
                'hari_lalu'        => 1,
            ],
            [
                'kode_nomenklatur' => '800',
                'nomor_urut'       => 6,
                'penerima'         => 'Kepala BKPSDM Kabupaten Lamongan',
                'alamat_tujuan'    => 'Jl. KH. Ahmad Dahlan, Lamongan',
                'kode_kategori'    => '800',
                'perihal'          => 'Usulan Peserta Pelatihan Manajemen Kepemimpinan Pengawas (PKP) bagi Pejabat Struktural Eselon IV',
                'isi_surat'        => "Menindaklanjuti surat edaran pengembangan kompetensi aparatur sipil negara, dengan ini Kecamatan Lamongan mengusulkan 2 (dua) nama pejabat pengawas teknis untuk diikutsertakan dalam seleksi calon peserta Diklat PKP Angkatan I TA 2026.",
                'sifat'            => 'Biasa',
                'status'           => 'Menunggu Persetujuan',
                'hari_lalu'        => 4,
            ],
        ];

        foreach ($dummySuratKeluar as $item) {
            $katId = $kategoriMap[$item['kode_kategori']] ?? KategoriSurat::first()?->id;
            $kodeInstansi = 'KEC.LMG';
            $nomorSurat = sprintf('%s/%03d/%s/%d', $item['kode_nomenklatur'], $item['nomor_urut'], $kodeInstansi, $tahunSekarang);

            $tglSuratKeluar = Carbon::now()->subDays($item['hari_lalu']);

            SuratKeluar::updateOrCreate(
                ['nomor_surat' => $nomorSurat],
                [
                    'kode_nomenklatur' => $item['kode_nomenklatur'],
                    'nomor_urut'       => $item['nomor_urut'],
                    'kode_instansi'    => $kodeInstansi,
                    'tahun'            => (int) $tahunSekarang,
                    'penerima'         => $item['penerima'],
                    'alamat_tujuan'    => $item['alamat_tujuan'],
                    'perihal'          => $item['perihal'],
                    'isi_surat'        => $item['isi_surat'],
                    'tanggal_surat'    => $tglSuratKeluar->toDateString(),
                    'kategori_id'      => $katId,
                    'file_path'        => $sampleFilePath,
                    'sifat'            => $item['sifat'],
                    'status'           => $item['status'],
                    'created_by'       => $adminId,
                ]
            );
        }
    }
}
