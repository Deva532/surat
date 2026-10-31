<?php

namespace App\Filament\Widgets;

use App\Models\SuratMasuk;
use Filament\Widgets\ChartWidget;

class SuratStatusChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Status Surat Masuk';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $user = auth()->user();

        $query = SuratMasuk::query()->forUser($user);
        $counts = (clone $query)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->toArray();

        $labels = ['Diarsipkan', 'Proses Disposisi', 'Selesai'];
        $data = [
            $counts['Diarsipkan'] ?? 0,
            $counts['Proses Disposisi'] ?? 0,
            $counts['Selesai'] ?? 0,
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Surat',
                    'data' => $data,
                    'backgroundColor' => [
                        '#64748b', // slate
                        '#f59e0b', // amber
                        '#10b981', // emerald
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
