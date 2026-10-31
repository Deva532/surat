<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfilResource\Pages;
use App\Models\Profil;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProfilResource extends Resource
{
    protected static ?string $model = Profil::class;

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Profil Instansi';

    protected static ?string $modelLabel = 'Profil Instansi';

    protected static ?string $pluralModelLabel = 'Profil Instansi';

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return Profil::count() === 0;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Profil Instansi')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Identitas Instansi')
                            ->schema([
                                Forms\Components\TextInput::make('instansi')
                                    ->label('Pemerintah Daerah')
                                    ->required()
                                    ->placeholder('Contoh: Pemerintah Kabupaten Lamongan'),
                                Forms\Components\TextInput::make('dinas')
                                    ->label('Dinas / Satuan Kerja')
                                    ->required()
                                    ->placeholder('Contoh: Kantor Kecamatan'),
                                Forms\Components\TextInput::make('nama_sekolah')
                                    ->label('Nama Instansi / Kantor')
                                    ->required()
                                    ->placeholder('Contoh: Kecamatan Lamongan'),
                                Forms\Components\TextInput::make('kode_lembaga')
                                    ->label('Kode Instansi / Lembaga')
                                    ->placeholder('Contoh: KEC.LMG'),
                                Forms\Components\Textarea::make('alamat')
                                    ->label('Alamat Kantor')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('kecamatan')
                                    ->label('Kecamatan')
                                    ->required()
                                    ->default('Lamongan'),
                                Forms\Components\TextInput::make('kabupaten')
                                    ->label('Kabupaten')
                                    ->required()
                                    ->default('Lamongan'),
                                Forms\Components\TextInput::make('telepon')
                                    ->label('Nomor Telepon')
                                    ->tel(),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email Resmi')
                                    ->email(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Pimpinan Instansi')
                            ->schema([
                                Forms\Components\TextInput::make('kepala_sekolah')
                                    ->label('Nama Camat / Kepala Instansi')
                                    ->required()
                                    ->placeholder('Contoh: H. Ahmad Fauzi, S.STP., M.Si'),
                                Forms\Components\TextInput::make('nip_kepala_sekolah')
                                    ->label('NIP Pimpinan')
                                    ->placeholder('18 digit angka NIP'),
                                Forms\Components\TextInput::make('pangkat_pimpinan')
                                    ->label('Pangkat / Golongan Pimpinan')
                                    ->placeholder('Contoh: Pembina Tk. I (IV/b)')
                                    ->helperText('Ditampilkan di Lembar Disposisi bagian tanda tangan.'),
                                Forms\Components\Select::make('status')
                                    ->label('Status Jabatan')
                                    ->options([
                                        'Definitif' => 'Definitif',
                                        'Plt' => 'Pelaksana Tugas (Plt)',
                                    ])
                                    ->default('Definitif')
                                    ->required(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Logo & KOP Surat')
                            ->schema([
                                Forms\Components\FileUpload::make('logo_pemda')
                                    ->label('Logo Pemda (Kiri KOP)')
                                    ->disk('public')
                                    ->directory('profile')
                                    ->image()
                                    ->maxSize(2048)
                                    ->helperText('Format PNG/JPG transparan, maks 2MB.'),
                                Forms\Components\FileUpload::make('logo')
                                    ->label('Logo Instansi / Tambahan (Kanan KOP)')
                                    ->disk('public')
                                    ->directory('profile')
                                    ->image()
                                    ->maxSize(2048)
                                    ->helperText('Format PNG/JPG transparan, maks 2MB.'),
                            ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_pemda')
                    ->label('Logo')
                    ->disk('public')
                    ->circular(),
                Tables\Columns\TextColumn::make('nama_sekolah')
                    ->label('Nama Instansi')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('instansi')
                    ->label('Pemda')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kepala_sekolah')
                    ->label('Pimpinan / Camat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telepon')
                    ->label('Telepon')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->placeholder('-'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProfils::route('/'),
            'create' => Pages\CreateProfil::route('/create'),
            'edit' => Pages\EditProfil::route('/{record}/edit'),
        ];
    }
}
