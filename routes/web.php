<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SuratPublikController;
use App\Models\Disposisi;
use App\Models\Profil;
use App\Models\SuratKeluar;

/*
|--------------------------------------------------------------------------
| Web Routes - SISTEM INFORMASI PERSURATAN KECAMATAN LAMONGAN
|--------------------------------------------------------------------------
*/

// --- 1. WEBSITE PENGIRIMAN SURAT PUBLIK ---
Route::get('/', [SuratPublikController::class, 'index'])->name('publik.index');
Route::post('/kirim-surat', [SuratPublikController::class, 'store'])
    ->middleware('throttle:30,1')
    ->name('publik.store');
Route::get('/kirim-surat/sukses', [SuratPublikController::class, 'sukses'])->name('publik.sukses');
Route::get('/lacak-surat', [SuratPublikController::class, 'lacak'])->name('publik.lacak');

// Redirect login bawaan ke Filament panel
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

// --- 2. LAYANAN CETAK LEMBAR DOKUMEN KEDINASAN ---
Route::middleware('auth')->group(function () {
    // Cetak Lembar Disposisi
    Route::get('/superadmin/desposisi/{disposisi}/cetak', function (Disposisi $disposisi) {
        $disposisi->load([
            'suratMasuk',
            'pengirim',
            'penerima',
            'penerimaList',
        ]);

        $profil = Profil::first();

        return view('laporan.cetak_desposisi', compact('disposisi', 'profil'));
    })->name('superadmin.desposisi.cetak');

    // Cetak Surat Keluar
    Route::get('/superadmin/suratkeluar/{suratKeluar}/cetak', function (SuratKeluar $suratKeluar) {
        $profil = Profil::firstOrFail();

        return view('laporan.cetak_surat_keluar', [
            'surat' => $suratKeluar,
            'profil' => $profil,
        ]);
    })->name('superadmin.suratkeluar.cetak');
});
