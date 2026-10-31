@extends('publik.layout')

@section('title', 'Formulir Pengiriman Surat — Kecamatan Lamongan')

@section('styles')
<style>
    /* HERO */
    .hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
        color: #ffffff;
        padding: 50px 0;
        margin-bottom: 36px;
    }

    .hero-content {
        max-width: 760px;
    }

    .hero-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
        margin-bottom: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .hero h2 {
        font-size: 32px;
        font-weight: 700;
        letter-spacing: -0.5px;
        margin-bottom: 12px;
        line-height: 1.25;
    }

    .hero p {
        font-size: 15px;
        color: #cbd5e1;
        line-height: 1.6;
    }

    /* STEP CARDS */
    .steps-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 36px;
    }

    .step-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 20px;
        display: flex;
        gap: 14px;
        align-items: flex-start;
    }

    .step-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #eff6ff;
        color: var(--primary);
        font-weight: 700;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .step-body h4 {
        font-size: 14px;
        font-weight: 600;
        color: var(--primary-dark);
        margin-bottom: 4px;
    }

    .step-body p {
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.5;
    }

    /* FORM CARD */
    .form-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .form-header {
        padding: 24px 30px;
        border-bottom: 1px solid var(--border-color);
        background: #ffffff;
    }

    .form-header h3 {
        font-size: 18px;
        font-weight: 700;
        color: var(--primary-dark);
    }

    .form-header p {
        font-size: 13px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .form-body {
        padding: 30px;
    }

    .section-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--primary-dark);
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 1px dashed var(--border-color);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }

    .col-span-2 {
        grid-column: span 2;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-main);
    }

    .form-label .required {
        color: var(--danger);
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        background-color: #ffffff;
        color: var(--text-main);
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    .input-hint {
        font-size: 12px;
        color: var(--text-muted);
    }

    .error-msg {
        font-size: 12px;
        color: var(--danger);
        font-weight: 500;
        margin-top: 2px;
    }

    /* RECIPIENT JABATAN SELECTION */
    .recipient-section {
        background: #f8fafc;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 20px;
        margin-bottom: 28px;
    }

    .recipient-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .counter-badge {
        background: #eff6ff;
        color: var(--primary);
        border: 1px solid #bfdbfe;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
    }

    .recipient-actions button {
        background: none;
        border: none;
        color: var(--primary);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        padding: 2px 6px;
        text-decoration: underline;
    }

    .jabatan-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .jabatan-item {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: border-color 0.2s, background-color 0.2s;
    }

    .jabatan-item:hover {
        border-color: #93c5fd;
        background-color: #f0f7ff;
    }

    .jabatan-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--primary);
    }

    .jabatan-label {
        font-size: 13px;
        font-weight: 500;
        color: var(--text-main);
        cursor: pointer;
        user-select: none;
    }

    /* FILE UPLOAD BOX */
    .file-box {
        border: 2px dashed #cbd5e1;
        border-radius: var(--radius);
        padding: 24px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: border-color 0.2s;
    }

    .file-box:hover {
        border-color: var(--primary);
    }

    .file-box input[type="file"] {
        display: none;
    }

    .file-icon {
        color: var(--primary);
        margin-bottom: 8px;
    }

    .file-name-display {
        margin-top: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--success);
    }

    /* SUBMIT BUTTON */
    .btn-submit {
        background-color: var(--primary);
        color: #ffffff;
        border: none;
        padding: 14px 28px;
        font-size: 15px;
        font-weight: 600;
        border-radius: 6px;
        cursor: pointer;
        width: 100%;
        transition: background-color 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-submit:hover {
        background-color: var(--primary-dark);
    }

    .btn-submit:disabled {
        background-color: var(--secondary);
        cursor: not-allowed;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .steps-grid {
            grid-template-columns: 1fr;
        }
        .form-grid {
            grid-template-columns: 1fr;
        }
        .col-span-2 {
            grid-column: span 1;
        }
        .jabatan-list {
            grid-template-columns: 1fr;
        }
        .hero h2 {
            font-size: 24px;
        }
    }
</style>
@endsection

@section('content')
<!-- HERO -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <span class="hero-badge">Pemerintah Kabupaten Lamongan</span>
            <h2>Layanan Pengiriman Surat Online</h2>
            <p>Kirimkan surat dinas, permohonan, atau dokumen resmi langsung kepada Kantor Kecamatan Lamongan beserta 7 pejabat struktural terkait secara cepat, transparan, dan terdata resmi.</p>
        </div>
    </div>
</section>

<div class="container">
    <!-- TATA CARA -->
    <div class="steps-grid">
        <div class="step-card">
            <div class="step-number">1</div>
            <div class="step-body">
                <h4>Isi Identitas & Dokumen</h4>
                <p>Lengkapi informasi pengirim, nomor surat, perihal, dan unggah berkas surat Anda (PDF/DOCX/JPG).</p>
            </div>
        </div>
        <div class="step-card">
            <div class="step-number">2</div>
            <div class="step-body">
                <h4>Pilih Jabatan Penerima</h4>
                <p>Pilih satu atau beberapa dari 7 jabatan struktural yang menjadi tujuan disposisi surat.</p>
            </div>
        </div>
        <div class="step-card">
            <div class="step-number">3</div>
            <div class="step-body">
                <h4>Terima Bukti Agenda</h4>
                <p>Dapatkan nomor agenda registrasi resmi sebagai tanda terima dan lacak status proses tindak lanjut.</p>
            </div>
        </div>
    </div>

    <!-- FORMULIR -->
    <div class="form-card">
        <div class="form-header">
            <h3>Formulir Pengiriman Surat Masuk</h3>
            <p>Pastikan seluruh data yang dimasukkan akurat dan dokumen pendukung telah siap.</p>
        </div>

        <form action="{{ route('publik.store') }}" method="POST" enctype="multipart/form-data" id="suratForm" class="form-body">
            @csrf

            <!-- 1. DATA PENGIRIM & SURAT -->
            <div class="section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                1. Identitas Pengirim & Nomor Surat
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="nama_pengirim">Nama Lengkap / Identitas Pengirim <span class="required">*</span></label>
                    <input type="text" id="nama_pengirim" name="nama_pengirim" class="form-control" value="{{ old('nama_pengirim') }}" placeholder="Contoh: Budi Santoso" required>
                    @error('nama_pengirim') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="instansi_pengirim">Instansi / Asal Surat</label>
                    <input type="text" id="instansi_pengirim" name="instansi_pengirim" class="form-control" value="{{ old('instansi_pengirim') }}" placeholder="Contoh: Desa Made / Dinas Pertanian / Pribadi">
                    @error('instansi_pengirim') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="kontak_pengirim">Nomor Telepon / WhatsApp</label>
                    <input type="text" id="kontak_pengirim" name="kontak_pengirim" class="form-control" value="{{ old('kontak_pengirim') }}" placeholder="Contoh: 081234567890">
                    <span class="input-hint">Untuk konfirmasi atau pemberitahuan tindak lanjut.</span>
                    @error('kontak_pengirim') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="kategori_id">Kategori Klasifikasi Surat <span class="required">*</span></label>
                    <select id="kategori_id" name="kategori_id" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($kategoriList as $kat)
                            <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                                [{{ $kat->kode_kategori }}] {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="nomor_surat">Nomor Surat Asli <span class="required">*</span></label>
                    <input type="text" id="nomor_surat" name="nomor_surat" class="form-control" value="{{ old('nomor_surat') }}" placeholder="Contoh: 005/123/Desa/2026" required>
                    @error('nomor_surat') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="tanggal_surat">Tanggal Surat <span class="required">*</span></label>
                    <input type="date" id="tanggal_surat" name="tanggal_surat" class="form-control" value="{{ old('tanggal_surat', date('Y-m-d')) }}" required>
                    @error('tanggal_surat') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- 2. PERIHAL & KETERANGAN -->
            <div class="section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                2. Perihal & Keterangan Surat
            </div>

            <div class="form-grid">
                <div class="form-group col-span-2">
                    <label class="form-label" for="perihal">Perihal / Hal Surat <span class="required">*</span></label>
                    <input type="text" id="perihal" name="perihal" class="form-control" value="{{ old('perihal') }}" placeholder="Contoh: Permohonan Dispensasi Pelayanan KTP" required>
                    @error('perihal') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group col-span-2">
                    <label class="form-label" for="ringkasan">Ringkasan / Catatan Tambahan (Opsional)</label>
                    <textarea id="ringkasan" name="ringkasan" class="form-control" rows="3" placeholder="Tuliskan pokok isi surat atau hal yang perlu diperhatikan..."></textarea>
                    @error('ringkasan') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- 3. PILIHAN PENERIMA DARI 7 JABATAN -->
            <div class="section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                3. Pilihan Penerima (7 Jabatan Struktural) <span class="required">*</span>
            </div>

            <div class="recipient-section">
                <div class="recipient-header">
                    <div>
                        <span class="counter-badge" id="counterBadge">0 dari 7 Jabatan Dipilih</span>
                    </div>
                    <div class="recipient-actions">
                        <button type="button" onclick="selectAllJabatan(true)">Pilih Semua</button>
                        <span>|</span>
                        <button type="button" onclick="selectAllJabatan(false)">Batalkan Semua</button>
                    </div>
                </div>

                <div class="jabatan-list">
                    @foreach ($jabatanList as $index => $jabatan)
                        <label class="jabatan-item" for="jabatan_{{ $index }}">
                            <input type="checkbox" id="jabatan_{{ $index }}" name="penerima[]" value="{{ $jabatan }}" class="jabatan-checkbox" onchange="updateCounter()" {{ (is_array(old('penerima')) && in_array($jabatan, old('penerima'))) ? 'checked' : '' }}>
                            <span class="jabatan-label">{{ $jabatan }}</span>
                        </label>
                    @endforeach
                </div>
                @error('penerima') <span class="error-msg" style="display:block; margin-top: 10px;">{{ $message }}</span> @enderror
            </div>

            <!-- 4. UPLOAD BERKAS -->
            <div class="section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                4. Lampiran Dokumen Surat <span class="required">*</span>
            </div>

            <div class="form-group" style="margin-bottom: 30px;">
                <label class="file-box" for="file_lampiran" id="fileBox">
                    <div class="file-icon">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="12" y1="18" x2="12" y2="12"/>
                            <line x1="9" y1="15" x2="15" y2="15"/>
                        </svg>
                    </div>
                    <p style="font-size: 14px; font-weight: 600; color: var(--primary);">Klik atau seret berkas surat ke sini</p>
                    <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Format didukung: PDF, DOCX, DOC, JPG, atau PNG (Maksimal 10 MB)</p>
                    <div class="file-name-display" id="fileNameDisplay"></div>
                    <input type="file" id="file_lampiran" name="file_lampiran" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required onchange="handleFileSelected(this)">
                </label>
                @error('file_lampiran') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <!-- TOMBOL KIRIM -->
            <button type="submit" class="btn-submit" id="submitBtn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
                Kirimkan Surat Sekarang
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateCounter() {
        const checkboxes = document.querySelectorAll('.jabatan-checkbox:checked');
        const count = checkboxes.length;
        const badge = document.getElementById('counterBadge');
        badge.textContent = `${count} dari 7 Jabatan Dipilih`;
        if (count > 0) {
            badge.style.background = '#eff6ff';
            badge.style.color = '#1e3a8a';
            badge.style.borderColor = '#93c5fd';
        } else {
            badge.style.background = '#fef2f2';
            badge.style.color = '#ef4444';
            badge.style.borderColor = '#fca5a5';
        }
    }

    function selectAllJabatan(checked) {
        document.querySelectorAll('.jabatan-checkbox').forEach(cb => {
            cb.checked = checked;
        });
        updateCounter();
    }

    function handleFileSelected(input) {
        const display = document.getElementById('fileNameDisplay');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            display.textContent = `✓ Berkas dipilih: ${file.name} (${sizeMB} MB)`;
        } else {
            display.textContent = '';
        }
    }

    // Anti-double submit
    document.getElementById('suratForm').addEventListener('submit', function (e) {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="animate-spin" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path>
            </svg>
            Sedang Mengirim Surat...
        `;
    });

    // Inisialisasi hitungan counter saat load
    document.addEventListener('DOMContentLoaded', updateCounter);
</script>
<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
@endsection
