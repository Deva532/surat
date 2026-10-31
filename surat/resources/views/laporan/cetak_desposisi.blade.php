<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Disposisi Surat</title>

    <style>
        @page {
            size: 215mm 165mm;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
        }

        .halaman {
            width: 100%;
            max-width: 199mm;
            margin: 0 auto;
        }

        /* KOP SURAT */
        .kop {
            display: table;
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
            margin-bottom: 0;
        }

        .kop-logo {
            display: table-cell;
            width: 75px;
            text-align: center;
            vertical-align: middle;
        }

        .kop-logo img {
            max-width: 62px;
            max-height: 70px;
            object-fit: contain;
        }

        .kop-identitas {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
        }

        .kop-identitas h3,
        .kop-identitas h4,
        .kop-identitas p {
            margin: 2px 0;
        }

        .kop-identitas h3 {
            font-size: 12px;
            font-weight: normal;
        }

        .kop-identitas h4 {
            font-size: 11px;
        }

        .kop-identitas p {
            font-size: 9px;
        }

        /* JUDUL */
        .judul {
            text-align: center;
            font-size: 11px;
            letter-spacing: 3px;
            margin: 8px 0 0;
            padding: 4px;
            border: 1px solid #000;
            border-bottom: none;
            font-weight: bold;
        }

        /* TABEL DISPOSISI */
        table.tabel-disposisi {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .tabel-disposisi td,
        .tabel-disposisi th {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
            font-size: 9px;
            font-weight: normal;
            overflow-wrap: anywhere;
        }

        .kolom-surat {
            width: 40%;
        }

        .kolom-tanggal {
            width: 16%;
        }

        .kolom-segera {
            width: 16%;
        }

        .kolom-rahasia {
            width: 16%;
        }

        .kolom-sifat {
            width: 12%;
        }

        .label {
            display: block;
            margin-bottom: 3px;
        }

        .nilai {
            font-weight: normal;
        }

        .ruang {
            height: 10px;
        }

        .tinggi-identitas {
            height: 112px;
        }

        .tinggi-perihal {
            height: 100px;
        }

        .tinggi-penerima {
            height: 62px;
        }

        .tinggi-catatan {
            height: 115px;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .daftar-penerima {
            margin: 4px 0 0;
            padding: 0;
            list-style: none;
        }

        .daftar-penerima li {
            margin-bottom: 5px;
        }

        .garis-isian {
            display: inline-block;
            min-width: 110px;
            border-bottom: 1px dotted #000;
            height: 10px;
        }

        .ttd-kanan {
            text-align: left;
            padding-left: 15px !important;
        }

        .nama-ttd {
            margin-top: 42px;
            font-weight: bold;
            text-decoration: underline;
        }

        .nip-ttd {
            margin-top: 3px;
        }

        .aksi {
            text-align: right;
            margin-bottom: 10px;
        }

        @media print {
            .aksi {
                display: none !important;
            }

            .halaman {
                width: 100%;
                max-width: none;
                margin: 0;
            }

            body {
                margin: 0;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            table,
            tr,
            td {
                break-inside: avoid;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="halaman">

        <div class="aksi">
            <button onclick="window.print()">Cetak</button>
            <button onclick="window.close()">Tutup</button>
        </div>

        {{-- KOP SURAT --}}
        <div class="kop">

            <div class="kop-logo">
                @if ($profil && $profil->logo)
                    <img src="{{ asset('storage/' . $profil->logo_pemda) }}" alt="Logo Instansi">
                @endif
            </div>

            <div class="kop-identitas">
                <h3>{{ strtoupper($profil->instansi ?? 'PEMERINTAH DAERAH') }}</h3>
                <h4>{{ strtoupper($profil->dinas ?? '-') }}</h4>
                <h4>{{ strtoupper($profil->nama_sekolah ?? '-') }}</h4>

                <p>
                    {{ $profil->alamat ?? '-' }}
                    @if ($profil?->kecamatan)
                        , Kec. {{ $profil->kecamatan }}
                    @endif
                    @if ($profil?->kabupaten)
                        , Kab. {{ $profil->kabupaten }}
                    @endif
                </p>

                <p>
                    Email: {{ $profil->email ?? '-' }}
                    | Telp: {{ $profil->telepon ?? '-' }}
                </p>
            </div>

        </div>

        {{-- JUDUL LEMBAR --}}
        <div class="judul">
            L E M B A R &nbsp; D I S P O S I S I
        </div>

        {{-- TABEL IDENTITAS SURAT --}}
        <table class="tabel-disposisi">
            <colgroup>
                <col class="kolom-surat">
                <col class="kolom-tanggal">
                <col class="kolom-segera">
                <col class="kolom-rahasia">
                <col class="kolom-sifat">
            </colgroup>

            <tr>
                <td colspan="2" class="tinggi-identitas">
                    <span class="label">Surat dari:</span>
                    <div class="nilai">
                        {{ $disposisi->suratMasuk->pengirim ?? '-' }}
                    </div>

                    <div class="ruang"></div>

                    <span class="label">No. Surat:</span>
                    <div class="nilai">
                        {{ $disposisi->suratMasuk->nomor_surat ?? '-' }}
                    </div>

                    <div class="ruang"></div>

                    <span class="label">Tgl. Surat:</span>
                    <div class="nilai">
                        {{ $disposisi->suratMasuk?->tanggal_surat
    ? \Carbon\Carbon::parse($disposisi->suratMasuk->tanggal_surat)->translatedFormat('d F Y')
    : '-' }}
                    </div>
                </td>

                <td colspan="2">
                    <span class="label">Diterima Tgl:</span>
                    <div>
                        {{ $disposisi->created_at?->translatedFormat('d F Y') ?? '-' }}
                    </div>

                    <div class="ruang"></div>

                    <span class="label">No. Agenda:</span>
                    <div>
                        {{ $disposisi->suratMasuk->nomor_agenda ?? '-' }}
                    </div>

                    <div class="ruang"></div>

                    <span class="label">Sifat:</span>
                    <div class="text-center">
                        {{ $disposisi->sifat_instruksi ?? '-' }}
                    </div>
                </td>

                <td class="text-center">
                    Segera
                    <div class="ruang"></div>

                    @if ($disposisi->sifat_instruksi === 'Sangat Segera')
                        <strong>✓</strong>
                    @endif
                </td>

                <td class="text-center">
                    Rahasia
                    <div class="ruang"></div>

                    {{-- Centang jika ada penanda rahasia pada data surat --}}
                    @if (
                            isset($disposisi->suratMasuk->sifat_surat)
                            && strtolower($disposisi->suratMasuk->sifat_surat) === 'rahasia'
                        )
                        <strong>✓</strong>
                    @endif
                </td>
            </tr>

            {{-- PERIHAL DAN INSTRUKSI --}}
            <tr>
                <td colspan="2" class="tinggi-perihal">
                    <span class="label">Hal:</span>
                    <div class="nilai">
                        {{ $disposisi->suratMasuk->perihal ?? '-' }}
                    </div>
                </td>

                <td colspan="4" class="tinggi-perihal">
                    <span class="label">Isi Disposisi / Instruksi:</span>
                    <div class="nilai">
                        {!! nl2br(e($disposisi->isi_disposisi ?? '-')) !!}
                    </div>
                </td>
            </tr>

            {{-- PENERIMA DAN HARAPAN TINDAK LANJUT --}}
            <tr>
                <td colspan="2" class="tinggi-penerima">
                    <span class="label">Diteruskan kepada sdr.:</span>

                    <div style="font-weight: bold; font-size: 10px;">
                        {{ $disposisi->penerima->jabatan ?? $disposisi->penerima->name ?? '-' }}
                    </div>

                    @if ($disposisi->penerima?->jabatan && $disposisi->penerima->name !== $disposisi->penerima->jabatan)
                        <div>
                            u.p. {{ $disposisi->penerima->name }}
                        </div>
                    @endif

                    <div>
                        NIP. {{ $disposisi->penerima->nip ?? '-' }}
                    </div>

                    <div class="ruang"></div>

                    Dan seterusnya.
                </td>

                <td colspan="4" class="tinggi-penerima">
                    <span class="label">Dengan hormat harap:</span>

                    <div class="nilai">
                        {{ $disposisi->catatan_tindak_lanjut
    ?: 'Tanggapan dan Saran' }}
                    </div>

                    <div class="ruang"></div>

                    <div>
                        Status: {{ $disposisi->status ?? '-' }}
                    </div>

                    @if ($disposisi->batas_waktu)
                        <div>
                            Batas waktu:
                            {{ \Carbon\Carbon::parse($disposisi->batas_waktu)->translatedFormat('d F Y') }}
                        </div>
                    @endif
                </td>
            </tr>

            {{-- CATATAN DAN TANDA TANGAN --}}
            <tr>
                <td colspan="6" class="tinggi-catatan">
                    <span class="label">Catatan:</span>

                    <div class="nilai">
                        {!! nl2br(e($disposisi->catatan_tindak_lanjut ?? '-')) !!}
                    </div>

                    <table style="width: 100%; border: none; margin-top: 12px;">
                        <tr>
                            <td style="width: 50%; border: none;"></td>
                            <td style="width: 50%; border: none; text-align: left;">
                                {{ $profil->kabupaten ?? '' }},
                                {{ now()->locale('id')->translatedFormat('d F Y') }}

                                <br>

                                @if (strtolower($profil->status ?? 'definitif') === 'plt')
                                    Plt. Kepala {{ $profil->nama_sekolah ?? '-' }}
                                @else
                                    Kepala {{ $profil->nama_sekolah ?? '-' }}
                                @endif

                                <div class="nama-ttd">
                                    {{ $profil->kepala_sekolah ?? '............................' }}
                                </div>

                                <div class="nip-ttd">
                                    NIP. {{ $profil->nip_kepala_sekolah ?? '-' }}
                                </div>
                            </td>
                </td>
            </tr>
        </table>
        </td>
        </tr>
        </table>

    </div>

</body>

</html>