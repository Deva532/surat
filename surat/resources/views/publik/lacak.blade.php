@extends('publik.layout')

@section('title', 'Pelacakan Status Surat — Kecamatan Lamongan')

@section('styles')
<style>
    .track-wrapper {
        max-width: 760px;
        margin: 40px auto;
    }

    .track-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        padding: 30px;
        margin-bottom: 30px;
    }

    .track-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--primary-dark);
        margin-bottom: 6px;
    }

    .track-desc {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 24px;
    }

    .search-box {
        display: flex;
        gap: 10px;
    }

    .search-input {
        flex: 1;
        padding: 12px 16px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .btn-search {
        background: var(--primary);
        color: #ffffff;
        border: none;
        padding: 12px 24px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn-search:hover {
        background: var(--primary-dark);
    }

    /* RESULT */
    .result-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .result-header {
        background: #f8fafc;
        border-bottom: 1px solid var(--border-color);
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .result-agenda {
        font-size: 16px;
        font-weight: 700;
        color: var(--primary-dark);
    }

    .badge-status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-diarsipkan {
        background: #f1f5f9;
        color: #475569;
    }

    .status-proses {
        background: #fef3c7;
        color: #b45309;
    }

    .status-selesai {
        background: #d1fae5;
        color: #065f46;
    }

    .result-body {
        padding: 24px;
    }

    .progress-bar-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 20px 0 30px;
        position: relative;
    }

    .progress-line {
        position: absolute;
        top: 14px;
        left: 30px;
        right: 30px;
        height: 3px;
        background: #e2e8f0;
        z-index: 1;
    }

    .progress-step {
        position: relative;
        z-index: 2;
        text-align: center;
        width: 100px;
    }

    .step-dot {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #ffffff;
        border: 3px solid #cbd5e1;
        margin: 0 auto 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
    }

    .step-active .step-dot {
        border-color: var(--primary);
        background: var(--primary);
        color: #ffffff;
    }

    .step-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .step-active .step-label {
        color: var(--primary-dark);
    }

    .recipient-list-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--primary-dark);
        margin: 20px 0 10px;
    }

    .recipient-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        background: #f8fafc;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-muted);
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="track-wrapper">
        <div class="track-card">
            <h2 class="track-title">Lacak Status Surat Masuk</h2>
            <p class="track-desc">Masukkan Nomor Agenda Registrasi Resmi atau Nomor Surat pengirim Anda untuk memantau perkembangan penanganan surat di Kecamatan Lamongan.</p>

            <form action="{{ route('publik.lacak') }}" method="GET" class="search-box">
                <input type="text" name="keyword" value="{{ $keyword }}" class="search-input" placeholder="Contoh: 400.3.5/001/KEC.LMG/2026 atau No. Surat" required>
                <button type="submit" class="btn-search">Lacak Surat</button>
            </form>
        </div>

        @if (!empty($keyword))
            @if ($hasil)
                <div class="result-card">
                    <div class="result-header">
                        <div>
                            <span style="font-size: 12px; color: var(--text-muted); display: block;">Nomor Agenda Registrasi</span>
                            <span class="result-agenda">{{ $hasil->nomor_agenda }}</span>
                        </div>
                        <div>
                            @php
                                $statusClass = match ($hasil->status) {
                                    'Diarsipkan' => 'status-diarsipkan',
                                    'Proses Disposisi' => 'status-proses',
                                    'Selesai' => 'status-selesai',
                                    default => 'status-diarsipkan',
                                };
                            @endphp
                            <span class="badge-status {{ $statusClass }}">Status: {{ $hasil->status }}</span>
                        </div>
                    </div>

                    <div class="result-body">
                        <!-- PROGRESS STEP -->
                        <div class="progress-bar-container">
                            <div class="progress-line"></div>
                            <div class="progress-step step-active">
                                <div class="step-dot">1</div>
                                <div class="step-label">Terkirim</div>
                            </div>
                            <div class="progress-step {{ in_array($hasil->status, ['Proses Disposisi', 'Selesai']) ? 'step-active' : '' }}">
                                <div class="step-dot">2</div>
                                <div class="step-label">Disposisi</div>
                            </div>
                            <div class="progress-step {{ $hasil->status === 'Selesai' ? 'step-active' : '' }}">
                                <div class="step-dot">3</div>
                                <div class="step-label">Selesai</div>
                            </div>
                        </div>

                        <table style="width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 16px;">
                            <tr>
                                <td style="padding: 6px 0; width: 35%; color: var(--text-muted); font-weight: 500;">Nomor Surat Pengirim</td>
                                <td style="padding: 6px 0; font-weight: 600;">{{ $hasil->nomor_surat }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 6px 0; color: var(--text-muted); font-weight: 500;">Asal Pengirim</td>
                                <td style="padding: 6px 0;">{{ $hasil->pengirim }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 6px 0; color: var(--text-muted); font-weight: 500;">Tanggal Surat Diterima</td>
                                <td style="padding: 6px 0;">{{ \Carbon\Carbon::parse($hasil->tanggal_diterima)->translatedFormat('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 6px 0; color: var(--text-muted); font-weight: 500;">Perihal</td>
                                <td style="padding: 6px 0;">{{ $hasil->perihal }}</td>
                            </tr>
                        </table>

                        <div class="recipient-list-title">Status Penanganan per Jabatan:</div>
                        @forelse ($hasil->penerimaList as $p)
                            <div class="recipient-item">
                                <div>
                                    <strong>{{ $p->jabatan }}</strong>
                                    @if ($p->selesai_pada)
                                        <small style="display:block; color: var(--text-muted);">Diselesaikan: {{ $p->selesai_pada->translatedFormat('d M Y H:i') }}</small>
                                    @endif
                                </div>
                                <div>
                                    <span style="font-weight: 600; font-size: 12px; color: {{ $p->status === 'Selesai' ? '#047857' : ($p->status === 'Diproses' ? '#b45309' : '#475569') }};">
                                        {{ $p->status }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p style="font-size: 13px; color: var(--text-muted);">{{ $hasil->penerima_dituju }}</p>
                        @endforelse
                    </div>
                </div>
            @else
                <div class="track-card empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 12px; color: var(--text-muted);">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <h4>Surat Tidak Ditemukan</h4>
                    <p style="margin-top: 4px; font-size: 13px;">Tidak ada surat dengan nomor registrasi atau nomor surat "<strong>{{ $keyword }}</strong>". Mohon periksa kembali nomor agenda pada tanda terima Anda.</p>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
