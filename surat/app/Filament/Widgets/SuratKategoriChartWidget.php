<?php

namespace App\Filament\Widgets;

use App\Models\KategoriSurat;
use App\Models\SuratMasuk;
use Filament\Widgets\ChartWidget;

class SuratKategoriChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Berdasarkan Kategori Surat';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $user = auth()->user();

        $kategoris = KategoriSurat::withCount(['suratMasuk' => function ($q) use ($user) {
            $q->forUser($user);
        }])->get();

        $labels = [];
        $data = [];

        foreach ($kategoris as $k) {
            $labels[] = $k->nama_kategori;
            $data[] = $k->surat_masuk_count;
        }

        if (empty($labels)) {
            $labels = ['Belum Ada Kategori'];
            $data = [0];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Surat',
                    'data' => $data,
                    'backgroundColor' => [
                        '#3b82f6',
                        '#8b5cf6',
                        '#ec4899',
                        '#14b8a6',
                        '#f97316',
                        '#6366f1',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
