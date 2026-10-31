<?php

namespace App\Filament\Widgets;

use App\Models\SuratMasuk;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestSuratMasukWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Aktivitas Persuratan Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SuratMasuk::query()
                    ->forUser(auth()->user())
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nomor_agenda')
                    ->label('No. Agenda')
                    ->badge()
                    ->color('primary')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->searchable(),

                Tables\Columns\TextColumn::make('pengirim')
                    ->label('Pengirim')
                    ->limit(30),

                Tables\Columns\TextColumn::make('perihal')
                    ->label('Perihal')
                    ->limit(40),

                Tables\Columns\TextColumn::make('tanggal_surat')
                    ->label('Tgl Surat')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Diarsipkan' => 'gray',
                        'Proses Disposisi' => 'warning',
                        'Selesai' => 'success',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('lihat')
                    ->label('Buka')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (SuratMasuk $record): string => route('filament.admin.resources.surat-masuks.index')),
            ]);
    }
}
