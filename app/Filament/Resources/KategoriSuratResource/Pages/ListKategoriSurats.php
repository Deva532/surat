<?php

namespace App\Filament\Resources\KategoriSuratResource\Pages;

use App\Filament\Resources\KategoriSuratResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoriSurats extends ListRecords
{
    protected static string $resource = KategoriSuratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
