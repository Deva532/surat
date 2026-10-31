<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriSuratResource\Pages;
use App\Models\KategoriSurat;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KategoriSuratResource extends Resource
{
    protected static ?string $model = KategoriSurat::class;

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Kategori Surat';

    protected static ?string $modelLabel = 'Kategori Surat';

    protected static ?string $pluralModelLabel = 'Kategori Surat';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kategori Surat')
                    ->schema([
                        Forms\Components\TextInput::make('kode_kategori')
                            ->label('Kode Nomenklatur / Klasifikasi')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: 400.3.5'),
                        Forms\Components\TextInput::make('nama_kategori')
                            ->label('Nama Kategori Surat')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Surat Undangan Kedinasan'),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan Tambahan')
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->placeholder('Deskripsi peruntukan kategori...'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_kategori')
                    ->label('Kode Klasifikasi')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_kategori')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('surat_masuk_count')
                    ->label('Surat Masuk')
                    ->counts('suratMasuk')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Tables\Actions\DeleteAction $action, KategoriSurat $record) {
                        $hasMasuk = SuratMasuk::where('kategori_id', $record->id)->exists();
                        $hasKeluar = SuratKeluar::where('kategori_id', $record->id)->exists();

                        if ($hasMasuk || $hasKeluar) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Menghapus')
                                ->body('Kategori ini masih digunakan oleh surat masuk atau surat keluar.')
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKategoriSurats::route('/'),
            'create' => Pages\CreateKategoriSurat::route('/create'),
            'edit' => Pages\EditKategoriSurat::route('/{record}/edit'),
        ];
    }
}
