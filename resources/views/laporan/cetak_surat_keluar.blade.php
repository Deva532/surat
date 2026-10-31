<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Keluar - {{ $surat->nomor_surat }}</title>

    <style>
        @page {
            size: A4;
            margin: 2cm 2.5cm;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            color: #000;
            line-height: 1.5;
        }

        .kop {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            text-align: center;
        }

        .logo {
            width: 85px;
            height: 85px;
            object-fit: contain;
        }

        .kop-text {
            flex: 1;
        }

        .kop-text h2,
        .kop-text h3,
        .kop-text p {
            margin: 0;
        }

        .kop-text h2 {
            font-size: 15pt;
        }

        .kop-text h3 {
            font-size: 14pt;
        }

        .kop-text p {
            font-size: 10pt;
        }

        .judul {
            text-align: center;
            margin-top: 25px;
            margin-bottom: 20px;
        }

        .judul h3 {
            margin: 0;
            text-decoration: underline;
        }

        .nomor {
            margin-top: 0;
        }

        .isi {
            margin-top: 25px;
            text-align: justify;
            white-space: pre-wrap;
        }

        .ttd {
            width: 42%;
            margin-left: auto;
            margin-top: 35px;
            text-align: center;
        }

        .nama {
            margin-top: 75px;
            font-weight: bold;
            text-decoration: underline;
        }

        .no-print {
            margin-bottom: 20px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button onclick="window.print()">Cetak / Simpan sebagai PDF</button>
    </div>

    <div class="kop">
        @if ($profil->logo)
            <img class="logo" src="{{ asset('storage/' . ltrim($profil->logo, '/')) }}" alt="Logo Instansi">
        @endif

        <div class="kop-text">
            @if ($profil->instansi)
                <h3>{{ strtoupper($profil->instansi) }}</h3>
            @endif

            @if ($profil->dinas)
                <h2>{{ strtoupper($profil->dinas) }}</h2>
            @endif

            @if ($profil->nama_sekolah)
                <h3>{{ strtoupper($profil->nama_sekolah) }}</h3>
            @endif

            <p>
                {{ $profil->alamat }}
                @if ($profil->kecamatan)
                    , Kecamatan {{ $profil->kecamatan }}
                @endif
                @if ($profil->kabupaten)
                    , {{ $profil->kabupaten }}
                @endif
            </p>

            <p>
                @if ($profil->telepon)
                    Telepon: {{ $profil->telepon }}
                @endif

                @if ($profil->email)
                    | Email: {{ $profil->email }}
                @endif
            </p>
        </div>
    </div>

    <div class="judul">
        <h3>SURAT</h3>
        <p class="nomor">
            Nomor: {{ $surat->nomor_surat }}
        </p>
    </div>

    <table>
        <tr>
            <td style="width: 100px;">Sifat</td>
            <td>: {{ $surat->sifat }}</td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>: -</td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>: {{ $surat->perihal }}</td>
        </tr>
    </table>

    <div style="margin-top: 25px;">
        Yth. {{ $surat->penerima }}<br>

        @if ($surat->alamat_tujuan)
            {{ $surat->alamat_tujuan }}<br>
        @endif

        di Tempat
    </div>

    <div class="isi">{{ $surat->isi_surat }}</div>

    <div class="ttd">
        <p>
            {{ $profil->kabupaten }},
            {{ $surat->tanggal_surat->locale('id')->translatedFormat('d F Y') }}
        </p>

        <p>
            Kepala {{ $profil->nama_sekolah }}
        </p>

        <div class="nama">
            {{ $profil->kepala_sekolah }}
        </div>

        <p>
            NIP. {{ $profil->nip_kepala_sekolah }}
        </p>
    </div>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>

</body>

</html>