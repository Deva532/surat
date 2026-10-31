<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Disposisi — {{ $disposisi->suratMasuk->nomor_agenda ?? '' }}</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 15mm 20mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #000;
            background: #fff;
        }

        /* ===== KOP SURAT ===== */
        .kop {
            display: table;
            width: 100%;
            padding-bottom: 6px;
            margin-bottom: 0;
        }

        .kop-logo {
            display: table-cell;
            width: 80px;
            text-align: center;
            vertical-align: middle;
        }

        .kop-logo img {
            max-width: 68px;
            max-height: 75px;
            object-fit: contain;
        }

        .kop-identitas {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            padding: 0 10px;
        }

        .kop-identitas .instansi {
            font-size: 12pt;
            font-weight: normal;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .kop-identitas .dinas {
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 2px 0;
        }

        .kop-identitas .alamat {
            font-size: 8pt;
            margin-top: 3px;
        }

        .kop-garis {
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            height: 5px;
            margin-bottom: 0;
        }

        /* ===== JUDUL ===== */
        .judul-wrapper {
            text-align: center;
            border: 1px solid #000;
            border-top: none;
            padding: 5px 4px;
            margin-bottom: 0;
        }

        .judul-wrapper h2 {
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        /* ===== TABEL UTAMA DISPOSISI ===== */
        table.tbl {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .tbl td,
        .tbl th {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: top;
            font-size: 9pt;
            font-weight: normal;
        }

        .tbl td.label-cell {
            font-weight: normal;
        }

        /* ===== BARIS IDENTITAS SURAT ===== */
        .col-left  { width: 50%; }
        .col-right { width: 50%; }

        .field-label {
            font-weight: normal;
            display: block;
            margin-bottom: 2px;
        }

        .field-value {
            font-weight: bold;
            min-height: 14px;
        }

        .field-gap { height: 8px; }

        /* Checkbox styles */
        .cb-row {
            display: table;
            width: 100%;
            margin: 3px 0;
        }

        .cb-box {
            display: table-cell;
            width: 16px;
            font-size: 11pt;
            vertical-align: middle;
        }

        .cb-label {
            display: table-cell;
            vertical-align: middle;
            font-size: 9pt;
            padding-left: 4px;
        }

        /* ===== TINGGI BARIS ===== */
        .row-identitas { height: 100px; }
        .row-diteruskan { height: 130px; }
        .row-catatan    { height: 80px;  }
        .row-ttd        { height: 85px;  }

        /* ===== TANDA TANGAN ===== */
        .ttd-wrapper {
            padding: 4px 10px;
        }

        .ttd-kota {
            margin-bottom: 4px;
        }

        .ttd-jabatan {
            margin-bottom: 60px;
        }

        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
        }

        .ttd-pangkat,
        .ttd-nip {
            font-size: 9pt;
        }

        /* ===== AKSI (hanya layar) ===== */
        .aksi {
            text-align: right;
            margin-bottom: 10px;
        }

        .aksi button {
            padding: 4px 14px;
            margin-left: 6px;
            cursor: pointer;
            font-size: 10pt;
            border: 1px solid #333;
            border-radius: 4px;
            background: #f5f5f5;
        }

        @media print {
            .aksi {
                display: none !important;
            }

            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .tbl td, .tbl th {
                break-inside: avoid;
            }
        }
    </style>
</head>

@php
    $surat      = $disposisi->suratMasuk;
    $pengirimKt = $disposisi->pengirim;

    // Jabatan penerima dari JSON field (upgrade baru)
    $jabatanPenerima = $disposisi->penerima_jabatan ?? [];

    // Fallback: jika masih single penerima (data lama)
    if (empty($jabatanPenerima) && $disposisi->penerima) {
        $jabatanPenerima = [$disposisi->penerima->jabatan ?? $disposisi->penerima->name];
    }

    $instruksiDipilih = $disposisi->instruksi ?? [];

    // 7 jabatan resmi
    $jabatanResmi = [
        'Sekretaris Kecamatan',
        'Kepala Seksi Pemerintahan',
        'Kepala Seksi Pelayanan Publik',
        'Kepala Seksi Ketentraman dan Ketertiban Umum',
        'Kepala Seksi Pembangunan dan Pemberdayaan Masyarakat',
        'Kepala Sub Bagian Umum dan Kepegawaian',
        'Kepala Sub Bagian Perencanaan Evaluasi dan Keuangan',
    ];

    // Instruksi resmi
    $instruksiResmi = [
        'Tanggapan dan Saran',
        'Proses Lebih Lanjut',
        'Koordinasi/Konfirmasikan',
        'Lain-lain',
    ];

    // Sifat surat / sifat instruksi
    $sifatSurat     = $surat->sifat ?? '';
    $sifatInstruksi = $disposisi->sifat_instruksi ?? '';

    // Profil pimpinan (Camat)
    $namaCamat    = $profil->kepala_sekolah    ?? '............................';
    $pangkatCamat = $profil->pangkat_pimpinan  ?? '............................';
    $nipCamat     = $profil->nip_kepala_sekolah ?? '-';
    $statusCamat  = strtolower($profil->status ?? 'definitif');

    // Tanggal disposisi
    $tglDisposisi = $disposisi->tanggal_disposisi
        ? \Carbon\Carbon::parse($disposisi->tanggal_disposisi)->locale('id')->translatedFormat('d F Y')
        : now()->locale('id')->translatedFormat('d F Y');
@endphp

<body onload="window.print()">

    <div class="halaman">

        <div class="aksi">
            <button onclick="window.print()">&#128438; Cetak</button>
            <button onclick="window.close()">&#10005; Tutup</button>
        </div>

        {{-- ===== KOP SURAT ===== --}}
        <div class="kop">
            <div class="kop-logo">
                @if ($profil && $profil->logo_pemda)
                    <img src="{{ asset('storage/' . $profil->logo_pemda) }}" alt="Logo Kabupaten Lamongan">
                @else
                    <div style="width:68px;height:75px;border:1px dashed #999;display:flex;align-items:center;justify-content:center;font-size:8pt;color:#999;">LOGO</div>
                @endif
            </div>

            <div class="kop-identitas">
                <div class="instansi">{{ strtoupper($profil->instansi ?? 'Pemerintah Kabupaten Lamongan') }}</div>
                <div class="dinas">{{ strtoupper($profil->dinas ?? 'Kecamatan Lamongan') }}</div>
                @if ($profil && $profil->nama_sekolah && $profil->nama_sekolah !== $profil->dinas)
                    <div style="font-size:10pt; text-transform:uppercase;">{{ strtoupper($profil->nama_sekolah) }}</div>
                @endif
                <div class="alamat">
                    Alamat: {{ $profil->alamat ?? '-' }}
                    @if ($profil?->kecamatan), Kec. {{ $profil->kecamatan }}@endif
                    @if ($profil?->kabupaten), Kab. {{ $profil->kabupaten }}@endif
                    @if ($profil?->telepon) &nbsp;&#9743; {{ $profil->telepon }}@endif
                    @if ($profil?->email) &nbsp;&#9993; {{ $profil->email }}@endif
                </div>
            </div>
        </div>

        <div class="kop-garis"></div>

        {{-- ===== JUDUL ===== --}}
        <div class="judul-wrapper">
            <h2>L E M B A R &nbsp; D I S P O S I S I</h2>
        </div>

        {{-- ===== TABEL IDENTITAS SURAT ===== --}}
        <table class="tbl">
            <tr>
                {{-- Kolom Kiri: Identitas Surat --}}
                <td class="col-left row-identitas">
                    <span class="field-label">Surat dari :</span>
                    <div class="field-value">{{ $surat->pengirim ?? '-' }}</div>

                    <div class="field-gap"></div>

                    <span class="field-label">Nomor Surat :</span>
                    <div class="field-value">{{ $surat->nomor_surat ?? '-' }}</div>

                    <div class="field-gap"></div>

                    <span class="field-label">Tanggal Surat :</span>
                    <div class="field-value">
                        {{ $surat?->tanggal_surat
                            ? \Carbon\Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y')
                            : '-' }}
                    </div>

                    <div class="field-gap"></div>

                    <span class="field-label">Hal :</span>
                    <div class="field-value">{{ $surat->perihal ?? '-' }}</div>
                </td>

                {{-- Kolom Kanan: Penerimaan & Sifat --}}
                <td class="col-right row-identitas">
                    <span class="field-label">Diterima Tanggal :</span>
                    <div class="field-value">{{ $tglDisposisi }}</div>

                    <div class="field-gap"></div>

                    <span class="field-label">Nomor Agenda :</span>
                    <div class="field-value">{{ $surat->nomor_agenda ?? '-' }}</div>

                    <div class="field-gap"></div>

                    <span class="field-label">Sifat Surat :</span>
                    <div style="margin-top:3px;">
                        <div class="cb-row">
                            <span class="cb-box">{{ ($sifatInstruksi === 'Sangat Segera') ? '☑' : '☐' }}</span>
                            <span class="cb-label">Sangat Segera</span>
                        </div>
                        <div class="cb-row">
                            <span class="cb-box">{{ ($sifatInstruksi === 'Penting' || $sifatSurat === 'Segera') ? '☑' : '☐' }}</span>
                            <span class="cb-label">Segera</span>
                        </div>
                        <div class="cb-row">
                            <span class="cb-box">{{ ($sifatSurat === 'Rahasia') ? '☑' : '☐' }}</span>
                            <span class="cb-label">Rahasia</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- ===== TABEL DITERUSKAN & INSTRUKSI ===== --}}
        <table class="tbl">
            <tr>
                {{-- Kolom Kiri: Diteruskan kepada 7 Jabatan --}}
                <td class="col-left row-diteruskan">
                    <span class="field-label" style="font-weight:bold; margin-bottom:5px; display:block;">Diteruskan kepada Sdr. :</span>
                    @foreach ($jabatanResmi as $jabatan)
                        <div class="cb-row">
                            <span class="cb-box">{{ in_array($jabatan, $jabatanPenerima) ? '☑' : '☐' }}</span>
                            <span class="cb-label">{{ $jabatan }}</span>
                        </div>
                    @endforeach
                </td>

                {{-- Kolom Kanan: Instruksi Resmi --}}
                <td class="col-right row-diteruskan">
                    <span class="field-label" style="font-weight:bold; margin-bottom:5px; display:block;">Dengan hormat harap :</span>
                    @foreach ($instruksiResmi as $instruksi)
                        <div class="cb-row">
                            <span class="cb-box">{{ in_array($instruksi, $instruksiDipilih) ? '☑' : '☐' }}</span>
                            <span class="cb-label">{{ $instruksi }}</span>
                        </div>
                    @endforeach

                    @if ($disposisi->instruksi_lainnya)
                        <div style="margin-top:6px; padding-left:20px; font-size:8.5pt; color:#333;">
                            <em>{{ $disposisi->instruksi_lainnya }}</em>
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        {{-- ===== CATATAN PIMPINAN ===== --}}
        <table class="tbl">
            <tr>
                <td colspan="2" class="row-catatan">
                    <span class="field-label" style="font-weight:bold;">Catatan :</span>
                    <div style="margin-top:4px; min-height:45px;">
                        {!! nl2br(e($disposisi->isi_disposisi ?? '')) !!}
                    </div>
                </td>
            </tr>
        </table>

        {{-- ===== TANDA TANGAN ===== --}}
        <table class="tbl">
            <tr>
                <td class="col-left row-ttd">
                    {{-- Kolom kosong untuk kolom kiri / paraf penerima --}}
                </td>
                <td class="col-right row-ttd">
                    <div class="ttd-wrapper">
                        <div class="ttd-kota">
                            {{ $profil->kabupaten ?? 'Lamongan' }}, {{ $tglDisposisi }}
                        </div>
                        <div class="ttd-jabatan">
                            @if ($statusCamat === 'plt')
                                Plt. Camat {{ $profil->kecamatan ?? 'Lamongan' }}
                            @else
                                Camat {{ $profil->kecamatan ?? 'Lamongan' }}
                            @endif
                        </div>
                        <div class="ttd-nama">{{ $namaCamat }}</div>
                        <div class="ttd-pangkat">{{ $pangkatCamat }}</div>
                        <div class="ttd-nip">NIP. {{ $nipCamat }}</div>
                    </div>
                </td>
            </tr>
        </table>

    </div>

</body>

</html>