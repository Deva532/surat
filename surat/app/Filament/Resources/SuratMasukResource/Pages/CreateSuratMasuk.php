<?php

namespace App\Filament\Resources\SuratMasukResource\Pages;

use App\Filament\Resources\SuratMasukResource;
use App\Models\SuratMasukPenerima;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateSuratMasuk extends CreateRecord
{
    protected static string $resource = SuratMasukResource::class;

    protected function afterCreate(): void
    {
        $penerimaList = $this->data['penerima_jabatan'] ?? [];

        if (!empty($penerimaList)) {
            $record = $this->record;
            $record->penerima_dituju = implode(', ', $penerimaList);
            $record->save();

            foreach ($penerimaList as $jabatanName) {
                $user = User::where('jabatan', $jabatanName)->first();
                SuratMasukPenerima::create([
                    'surat_masuk_id' => $record->id,
                    'user_id' => $user?->id,
                    'jabatan' => $jabatanName,
                    'status' => 'Terkirim',
                ]);
            }
        }
    }
}
