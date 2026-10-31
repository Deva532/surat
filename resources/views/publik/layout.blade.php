<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Layanan Pengiriman Surat Online') — Kecamatan Lamongan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-dark: #0f172a;
            --primary-light: #3b82f6;
            --secondary: #64748b;
            --bg-page: #f8fafc;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --radius: 8px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-page);
            color: var(--text-main);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* HEADER & NAVBAR */
        .header {
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .container {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: var(--primary-dark);
        }

        .brand-logo {
            width: 44px;
            height: 44px;
            object-fit: contain;
        }

        .brand-text h1 {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: -0.2px;
            line-height: 1.2;
        }

        .brand-text p {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 400;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 24px;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-main);
            transition: color 0.2s;
        }

        .nav-links a:hover, .nav-links a.active {
            color: var(--primary);
        }

        .btn-panel {
            background-color: var(--primary);
            color: #ffffff !important;
            padding: 8px 18px;
            border-radius: var(--radius);
            font-weight: 600 !important;
            font-size: 13px !important;
            transition: background 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-panel:hover {
            background-color: var(--primary-dark);
        }

        /* FOOTER */
        .footer {
            background-color: #ffffff;
            border-top: 1px solid var(--border-color);
            padding: 40px 0 24px;
            margin-top: 60px;
            color: var(--text-muted);
            font-size: 13px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 32px;
            margin-bottom: 30px;
        }

        .footer-col h4 {
            color: var(--primary-dark);
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .footer-col p {
            line-height: 1.6;
        }

        .footer-bottom {
            border-top: 1px solid var(--border-color);
            padding-top: 20px;
            text-align: center;
            font-size: 12px;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .navbar {
                height: 64px;
            }
            .brand-text p {
                display: none;
            }
            .nav-links {
                gap: 12px;
            }
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="{{ route('publik.index') }}" class="brand">
                    @if (!empty($profil?->logo_pemda) && file_exists(public_path('storage/' . $profil->logo_pemda)))
                        <img src="{{ asset('storage/' . $profil->logo_pemda) }}" alt="Logo Instansi" class="brand-logo">
                    @elseif (file_exists(public_path('profile/logo_pemda_1765174620.png')))
                        <img src="{{ asset('profile/logo_pemda_1765174620.png') }}" alt="Logo Instansi" class="brand-logo">
                    @else
                        <div style="width:40px;height:40px;border-radius:6px;background:#1e3a8a;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;font-size:18px;">L</div>
                    @endif
                    <div class="brand-text">
                        <h1>SIPERSURAT</h1>
                        <p>Kecamatan Lamongan — Kab. Lamongan</p>
                    </div>
                </a>

                <ul class="nav-links">
                    <li><a href="{{ route('publik.index') }}">Kirim Surat</a></li>
                    <li><a href="{{ route('publik.lacak') }}">Pelacakan Surat</a></li>
                    <li>
                        <a href="{{ url('/admin') }}" class="btn-panel">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/>
                            </svg>
                            Panel Pegawai
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>Pemerintah Kecamatan Lamongan</h4>
                    <p>{{ $profil->alamat ?? 'Jl. Lamongan No. 1, Kabupaten Lamongan, Jawa Timur' }}</p>
                    <p style="margin-top: 6px;">Email: {{ $profil->email ?? 'kecamatan@lamongan.go.id' }} | Telp: {{ $profil->telepon ?? '(0322) 321xxx' }}</p>
                </div>
                <div class="footer-col">
                    <h4>Jam Pelayanan Kantor</h4>
                    <p>Senin – Kamis: 07.30 – 16.00 WIB</p>
                    <p>Jumat: 07.30 – 14.30 WIB</p>
                    <p>Sabtu & Minggu: Tutup</p>
                </div>
                <div class="footer-col">
                    <h4>Akses Internal</h4>
                    <p>Khusus Pegawai & 7 Jabatan Penerima untuk verifikasi dan tindak lanjut disposisi.</p>
                    <p style="margin-top: 8px;"><a href="{{ url('/admin') }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">&rarr; Masuk Panel Filament</a></p>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; {{ date('Y') }} Sistem Informasi Persuratan Online (SIPERSURAT) Kecamatan Lamongan. Hak Cipta Dilindungi Undang-Undang.
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
