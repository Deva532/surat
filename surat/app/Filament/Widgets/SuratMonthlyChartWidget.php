<?php

namespace App\Filament\Widgets;

use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SuratMonthlyChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Tren Persuratan Bulanan (Tahun Berjalan)';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $year = (int) date('Y');
        $user = auth()->user();

        $bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $masukCounts = array_fill(1, 12, 0);
        $keluarCounts = array_fill(1, 12, 0);

        // Agregasi Surat Masuk (role-aware)
        $queryMasuk = SuratMasuk::query()->forUser($user)->whereYear('tanggal_surat', $year);
        $masukData = (clone $queryMasuk)
            ->selectRaw('MONTH(tanggal_surat) as bulan, COUNT(*) as aggregate')
            ->groupBy('bulan')
            ->pluck('aggregate', 'bulan')
            ->toArray();

        foreach ($masukData as $bln => $total) {
            $masukCounts[(int) $bln] = (int) $total;
        }

        // Agregasi Surat Keluar
        $keluarData = SuratKeluar::whereYear('tanggal_surat', $year)
            ->selectRaw('MONTH(tanggal_surat) as bulan, COUNT(*) as aggregate')
            ->groupBy('bulan')
            ->pluck('aggregate', 'bulan')
            ->toArray();

        foreach ($keluarData as $bln => $total) {
            $keluarCounts[(int) $bln] = (int) $total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Surat Masuk',
                    'data' => array_values($masukCounts),
                    'borderColor' => '#2563eb', // navy / blue
                    'backgroundColor' => 'rgba(37, 99, 235, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Surat Keluar',
                    'data' => array_values($keluarCounts),
                    'borderColor' => '#0ea5e9', // sky blue
                    'backgroundColor' => 'rgba(14, 165, 233, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $bulanLabels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
