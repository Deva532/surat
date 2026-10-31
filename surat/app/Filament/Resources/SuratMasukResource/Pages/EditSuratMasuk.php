<?php

namespace App\Filament\Resources\SuratMasukResource\Pages;

use App\Filament\Resources\SuratMasukResource;
use App\Models\SuratMasukPenerima;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuratMasuk extends EditRecord
{
    protected static string $resource = SuratMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $penerimaList = $this->data['penerima_jabatan'] ?? null;

        if (is_array($penerimaList)) {
            $record = $this->record;
            $record->penerima_dituju = implode(', ', $penerimaList);
            $record->saveQuietly();

            $existing = SuratMasukPenerima::where('surat_masuk_id', $record->id)->pluck('jabatan')->toArray();

            // Tambahkan yang baru
            foreach ($penerimaList as $jabatanName) {
                if (!in_array($jabatanName, $existing, true)) {
                    $user = User::where('jabatan', $jabatanName)->first();
                    SuratMasukPenerima::create([
                        'surat_masuk_id' => $record->id,
                        'user_id' => $user?->id,
                        'jabatan' => $jabatanName,
                        'status' => 'Terkirim',
                    ]);
                }
            }

            // Hapus yang dibatalkan
            SuratMasukPenerima::where('surat_masuk_id', $record->id)
                ->whereNotIn('jabatan', $penerimaList)
                ->delete();
        }
    }
}
