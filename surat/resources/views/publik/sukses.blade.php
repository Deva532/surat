@extends('publik.layout')

@section('title', 'Tanda Terima Pengiriman Surat — Kecamatan Lamongan')

@section('styles')
<style>
    .success-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        max-width: 760px;
        margin: 40px auto;
        overflow: hidden;
    }

    .success-header {
        background: #ecfdf5;
        border-bottom: 1px solid #d1fae5;
        padding: 30px;
        text-align: center;
    }

    .success-icon {
        width: 56px;
        height: 56px;
        background: #10b981;
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }

    .success-header h2 {
        font-size: 22px;
        font-weight: 700;
        color: #065f46;
        margin-bottom: 6px;
    }

    .success-header p {
        font-size: 14px;
        color: #047857;
    }

    .success-body {
        padding: 30px;
    }

    .agenda-box {
        background: #eff6ff;
        border: 2px dashed #93c5fd;
        border-radius: 8px;
        padding: 18px;
        text-align: center;
        margin-bottom: 24px;
    }

    .agenda-label {
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 600;
        color: var(--primary);
        letter-spacing: 0.5px;
    }

    .agenda-value {
        font-size: 22px;
        font-weight: 800;
        color: var(--primary-dark);
        margin-top: 4px;
        letter-spacing: 1px;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
        font-size: 14px;
    }

    .info-table td {
        padding: 10px 14px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: top;
    }

    .info-table td:first-child {
        width: 35%;
        font-weight: 600;
        color: var(--text-muted);
    }

    .info-table td:last-child {
        color: var(--text-main);
    }

    .recipient-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .recipient-tag {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        font-size: 12px;
        font-weight: 500;
        padding: 3px 8px;
        border-radius: 4px;
        color: var(--text-main);
    }

    .notice-box {
        background: #fffbeb;
        border-left: 4px solid var(--warning);
        padding: 14px 18px;
        border-radius: 4px;
        margin-bottom: 24px;
        font-size: 13px;
        color: #92400e;
        line-height: 1.6;
    }

    .actions-bar {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .btn-primary {
        background: var(--primary);
        color: #ffffff;
        border: none;
    }

    .btn-secondary {
        background: #ffffff;
        color: var(--text-main);
        border: 1px solid var(--border-color);
    }

    @media print {
        .header, .footer, .actions-bar, .notice-box {
            display: none !important;
        }
        .success-card {
            box-shadow: none;
            border: 1px solid #000;
            margin: 0;
            max-width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="success-card">
        <div class="success-header">
            <div class="success-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <h2>Surat Berhasil Dikirimkan</h2>
            <p>Dokumen Anda telah masuk ke sistem persuratan resmi Kecamatan Lamongan.</p>
        </div>

        <div class="success-body">
            <div class="agenda-box">
                <div class="agenda-label">Nomor Agenda Registrasi Resmi</div>
                <div class="agenda-value">{{ $surat->nomor_agenda }}</div>
                <small style="color: var(--text-muted); font-size: 11px; margin-top: 4px; display: block;">Simpan nomor agenda ini untuk keperluan pelacakan atau verifikasi.</small>
            </div>

            <table class="info-table">
                <tr>
                    <td>Nomor Surat Pengirim</td>
                    <td><strong>{{ $surat->nomor_surat }}</strong></td>
                </tr>
                <tr>
                    <td>Asal Pengirim</td>
                    <td>{{ $surat->pengirim }}</td>
                </tr>
                <tr>
                    <td>Tanggal Surat</td>
                    <td>{{ \Carbon\Carbon::parse($surat->tanggal_surat)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td>Waktu Pengiriman</td>
                    <td>{{ $surat->created_at ? $surat->created_at->translatedFormat('d F Y H:i') . ' WIB' : '-' }}</td>
                </tr>
                <tr>
                    <td>Perihal</td>
                    <td>{{ $surat->perihal }}</td>
                </tr>
                <tr>
                    <td>Tujuan Penerima ({{ $surat->penerimaList->count() }} Jabatan)</td>
                    <td>
                        <div class="recipient-tags">
                            @forelse ($surat->penerimaList as $p)
                                <span class="recipient-tag">✓ {{ $p->jabatan }}</span>
                            @empty
                                <span class="recipient-tag">{{ $surat->penerima_dituju }}</span>
                            @endforelse
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Status Awal</td>
                    <td><span style="color: #b45309; font-weight: 600; background: #fef3c7; padding: 2px 8px; border-radius: 4px; font-size: 12px;">Terkirim — Menunggu Verifikasi & Disposisi</span></td>
                </tr>
            </table>

            <div class="notice-box">
                <strong>Langkah Berikutnya:</strong><br>
                Surat Anda sedang dalam antrean verifikasi staf administrasi Kecamatan Lamongan untuk kemudian diteruskan dan ditindaklanjuti oleh pejabat struktural yang bersangkutan sesuai hari dan jam kerja kedinasan.
            </div>

            <div class="actions-bar">
                <button type="button" onclick="window.print()" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"/>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                        <rect x="6" y="14" width="12" height="8"/>
                    </svg>
                    Cetak Tanda Terima
                </button>
                <a href="{{ route('publik.lacak', ['keyword' => $surat->nomor_agenda]) }}" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Lacak Status Surat
                </a>
                <a href="{{ route('publik.index') }}" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Kirim Surat Baru
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
