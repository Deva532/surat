<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Pengguna & Jabatan';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna & Jabatan';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Pegawai & Akun')
                    ->description('Kelola kredensial login dan jabatan kedinasan.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap / Jabatan Singkat')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Kepala Seksi Pemerintahan'),

                        Forms\Components\TextInput::make('nip')
                            ->label('NIP (Nomor Induk Pegawai)')
                            ->maxLength(30)
                            ->unique(ignoreRecord: true)
                            ->placeholder('18 digit angka NIP'),

                        Forms\Components\TextInput::make('email')
                            ->label('Email Kedinasan')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('nama@lamongan.go.id'),

                        Forms\Components\TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255)
                            ->helperText('Kosongkan jika tidak ingin mengubah kata sandi.'),

                        Forms\Components\Select::make('role')
                            ->label('Peran Akun')
                            ->options([
                                'superadmin' => 'Super Administrator (Akses Penuh)',
                                'admin' => 'Admin Persuratan',
                                'jabatan' => 'Pejabat / Jabatan Penerima Disposisi',
                            ])
                            ->default('jabatan')
                            ->required(),

                        Forms\Components\Select::make('jabatan')
                            ->label('Jabatan Kedinasan')
                            ->options([
                                'Sekretaris Kecamatan' => 'Sekretaris Kecamatan',
                                'Kepala Seksi Pemerintahan' => 'Kepala Seksi Pemerintahan',
                                'Kepala Seksi Pelayanan Publik' => 'Kepala Seksi Pelayanan Publik',
                                'Kepala Seksi Ketentraman dan Ketertiban Umum' => 'Kepala Seksi Ketentraman dan Ketertiban Umum',
                                'Kepala Seksi Pembangunan dan Pemberdayaan Masyarakat' => 'Kepala Seksi Pembangunan dan Pemberdayaan Masyarakat',
                                'Kepala Sub Bagian Umum dan Kepegawaian' => 'Kepala Sub Bagian Umum dan Kepegawaian',
                                'Kepala Sub Bagian Perencanaan Evaluasi dan Keuangan' => 'Kepala Sub Bagian Perencanaan Evaluasi dan Keuangan',
                            ])
                            ->searchable()
                            ->nullable()
                            ->placeholder('Pilih salah satu dari 7 jabatan resmi...'),

                        Forms\Components\TextInput::make('pangkat_golongan')
                            ->label('Pangkat / Golongan')
                            ->maxLength(100)
                            ->placeholder('Contoh: Pembina (IV/a)')
                            ->helperText('Pangkat dan golongan ruang pegawai, muncul di Lembar Disposisi.'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Pegawai / User')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->placeholder('Administrator'),

                Tables\Columns\TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-m-envelope'),

                Tables\Columns\TextColumn::make('role')
                    ->label('Peran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'superadmin' => 'danger',
                        'admin' => 'warning',
                        'jabatan' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'superadmin' => 'Super Admin',
                        'admin' => 'Admin',
                        'jabatan' => 'Jabatan',
                        default => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Filter Peran')
                    ->options([
                        'superadmin' => 'Super Admin',
                        'admin' => 'Admin',
                        'jabatan' => 'Jabatan',
                    ]),
                Tables\Filters\SelectFilter::make('jabatan')
                    ->label('Filter Jabatan')
                    ->options([
                        'Sekretaris Kecamatan' => 'Sekretaris Kecamatan',
                        'Kepala Seksi Pemerintahan' => 'Kepala Seksi Pemerintahan',
                        'Kepala Seksi Pelayanan Publik' => 'Kepala Seksi Pelayanan Publik',
                        'Kepala Seksi Ketentraman dan Ketertiban Umum' => 'Kepala Seksi Ketentraman dan Ketertiban Umum',
                        'Kepala Seksi Pembangunan dan Pemberdayaan Masyarakat' => 'Kepala Seksi Pembangunan dan Pemberdayaan Masyarakat',
                        'Kepala Sub Bagian Umum dan Kepegawaian' => 'Kepala Sub Bagian Umum dan Kepegawaian',
                        'Kepala Sub Bagian Perencanaan Evaluasi dan Keuangan' => 'Kepala Sub Bagian Perencanaan Evaluasi dan Keuangan',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
