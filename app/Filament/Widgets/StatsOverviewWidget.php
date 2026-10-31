<?php

namespace App\Filament\Widgets;

use App\Models\Disposisi;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\SuratMasukPenerima;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();

        if ($user && $user->isAdmin()) {
            // Statistik Keseluruhan untuk Administrator
            $totalMasuk = SuratMasuk::count();
            $totalKeluar = SuratKeluar::count();
            $menunggu = SuratMasuk::where('status', 'Diarsipkan')->count();
            $proses = SuratMasuk::where('status', 'Proses Disposisi')->count();
            $selesai = SuratMasuk::where('status', 'Selesai')->count();

            return [
                Stat::make('Total Surat Masuk', $totalMasuk)
                    ->description('Seluruh arsip surat masuk')
                    ->descriptionIcon('heroicon-m-inbox-arrow-down')
                    ->color('primary'),

                Stat::make('Total Surat Keluar', $totalKeluar)
                    ->description('Surat keluar diterbitkan')
                    ->descriptionIcon('heroicon-m-paper-airplane')
                    ->color('info'),

                Stat::make('Menunggu Disposisi', $menunggu)
                    ->description('Surat belum didisposisikan')
                    ->descriptionIcon('heroicon-m-clock')
                    ->color('gray'),

                Stat::make('Sedang Diproses', $proses)
                    ->description('Dalam tindak lanjut disposisi')
                    ->descriptionIcon('heroicon-m-arrow-path')
                    ->color('warning'),

                Stat::make('Surat Selesai', $selesai)
                    ->description('Telah selesai ditangani')
                    ->descriptionIcon('heroicon-m-check-circle')
                    ->color('success'),
            ];
        }

        // Statistik Khusus untuk Jabatan
        $myMasukQuery = SuratMasuk::query()->forUser($user);
        $totalMasuk = $myMasukQuery->count();

        $myDisposisiQuery = Disposisi::where('penerima_id', $user->id);
        $totalDisposisi = $myDisposisiQuery->count();
        $disposisiMenunggu = (clone $myDisposisiQuery)->where('status', 'Belum Dibaca')->count();
        $disposisiProses = (clone $myDisposisiQuery)->where('status', 'Proses')->count();
        $disposisiSelesai = (clone $myDisposisiQuery)->where('status', 'Selesai')->count();

        return [
            Stat::make('Surat Masuk Ditujukan', $totalMasuk)
                ->description('Surat relevan ke jabatan Anda')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('primary'),

            Stat::make('Total Disposisi Diterima', $totalDisposisi)
                ->description('Instruksi pimpinan')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('info'),

            Stat::make('Perlu Dibaca', $disposisiMenunggu)
                ->description('Disposisi baru belum dibuka')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger'),

            Stat::make('Sedang Ditindaklanjuti', $disposisiProses)
                ->description('Tahap penanganan aktif')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),

            Stat::make('Disposisi Selesai', $disposisiSelesai)
                ->description('Tindak lanjut tuntas')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
