<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuratKeluarResource\Pages;
use App\Models\KategoriSurat;
use App\Models\Profil;
use App\Models\SuratKeluar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SuratKeluarResource extends Resource
{
    protected static ?string $model = SuratKeluar::class;

    protected static ?string $navigationGroup = 'Persuratan';

    protected static ?string $navigationLabel = 'Surat Keluar';

    protected static ?string $modelLabel = 'Surat Keluar';

    protected static ?string $pluralModelLabel = 'Surat Keluar';

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Komponen Penomoran & Klasifikasi')
                            ->schema([
                                Forms\Components\Select::make('kategori_id')
                                    ->label('Kategori Klasifikasi')
                                    ->relationship('kategori', 'nama_kategori')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        if (!$state) return;
                                        $kategori = KategoriSurat::find($state);
                                        $profil = Profil::first();
                                        $kodeNomenklatur = trim($kategori->kode_kategori ?? '400');
                                        $kodeInstansi = strtoupper(trim($profil->kode_lembaga ?? 'KEC.LMG'));
                                        $tgl = $get('tanggal_surat') ?: now()->toDateString();
                                        $tahun = (int) date('Y', strtotime($tgl));

                                        $nomorUrut = ((int) SuratKeluar::withTrashed()
                                            ->where('kode_nomenklatur', $kodeNomenklatur)
                                            ->where('kode_instansi', $kodeInstansi)
                                            ->where('tahun', $tahun)
                                            ->max('nomor_urut')) + 1;

                                        $set('kode_nomenklatur', $kodeNomenklatur);
                                        $set('nomor_urut', $nomorUrut);
                                        $set('kode_instansi', $kodeInstansi);
                                        $set('tahun', $tahun);
                                        $set('nomor_surat', sprintf('%s/%03d/%s/%d', $kodeNomenklatur, $nomorUrut, $kodeInstansi, $tahun));
                                    }),

                                Forms\Components\TextInput::make('nomor_surat')
                                    ->label('Nomor Surat Keluar (Otomatis)')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Diformat otomatis sesuai kaidah tata naskah dinas.'),

                                Forms\Components\Hidden::make('kode_nomenklatur')
                                    ->default('400'),
                                Forms\Components\Hidden::make('nomor_urut')
                                    ->default(1),
                                Forms\Components\Hidden::make('kode_instansi')
                                    ->default('KEC.LMG'),
                                Forms\Components\Hidden::make('tahun')
                                    ->default(fn () => (int) date('Y')),

                                Forms\Components\DatePicker::make('tanggal_surat')
                                    ->label('Tanggal Surat')
                                    ->default(now())
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        $katId = $get('kategori_id');
                                        if ($katId && $state) {
                                            $kategori = KategoriSurat::find($katId);
                                            $profil = Profil::first();
                                            $kodeNomenklatur = trim($kategori->kode_kategori ?? '400');
                                            $kodeInstansi = strtoupper(trim($profil->kode_lembaga ?? 'KEC.LMG'));
                                            $tahun = (int) date('Y', strtotime($state));

                                            $nomorUrut = ((int) SuratKeluar::withTrashed()
                                                ->where('kode_nomenklatur', $kodeNomenklatur)
                                                ->where('kode_instansi', $kodeInstansi)
                                                ->where('tahun', $tahun)
                                                ->max('nomor_urut')) + 1;

                                            $set('tahun', $tahun);
                                            $set('nomor_surat', sprintf('%s/%03d/%s/%d', $kodeNomenklatur, $nomorUrut, $kodeInstansi, $tahun));
                                        }
                                    }),
                            ])->columns(2),

                        Forms\Components\Section::make('Tujuan & Isi Surat')
                            ->schema([
                                Forms\Components\TextInput::make('penerima')
                                    ->label('Penerima / Kepada Yth.')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: Kepala Dinas Lingkungan Hidup Kab. Lamongan'),

                                Forms\Components\Textarea::make('alamat_tujuan')
                                    ->label('Alamat / Tempat Tujuan')
                                    ->rows(2)
                                    ->placeholder('Contoh: Jl. Basuki Rahmat No. 1, Lamongan'),

                                Forms\Components\TextInput::make('perihal')
                                    ->label('Perihal Surat')
                                    ->required()
                                    ->maxLength(1000)
                                    ->columnSpanFull()
                                    ->placeholder('Contoh: Permohonan Bantuan Bibit Penghijauan'),

                                Forms\Components\RichEditor::make('isi_surat')
                                    ->label('Isi Surat Keluar')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Sifat & Status')
                            ->schema([
                                Forms\Components\Select::make('sifat')
                                    ->label('Sifat Surat')
                                    ->options([
                                        'Biasa' => 'Biasa',
                                        'Penting' => 'Penting',
                                        'Rahasia' => 'Rahasia',
                                        'Segera' => 'Segera',
                                    ])
                                    ->default('Biasa')
                                    ->required(),

                                Forms\Components\Select::make('status')
                                    ->label('Status Surat Keluar')
                                    ->options([
                                        'Draft' => 'Draft',
                                        'Menunggu Persetujuan' => 'Menunggu Persetujuan Camat',
                                        'Disetujui' => 'Disetujui',
                                        'Terkirim' => 'Terkirim ke Tujuan',
                                    ])
                                    ->default('Draft')
                                    ->required(),

                                Forms\Components\FileUpload::make('file_path')
                                    ->label('Lampiran / Berkas Pindai (Opsional)')
                                    ->disk('public')
                                    ->directory('suratkeluar')
                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'])
                                    ->maxSize(10240)
                                    ->helperText('Jika surat sudah ditandatangani dan dipindai.'),

                                Forms\Components\Hidden::make('created_by')
                                    ->default(fn () => auth()->id()),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_surat')
                    ->label('Nomor Surat Keluar')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('penerima')
                    ->label('Tujuan / Penerima')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('perihal')
                    ->label('Perihal')
                    ->searchable()
                    ->limit(35)
                    ->tooltip(fn (SuratKeluar $record): string => $record->perihal ?? ''),

                Tables\Columns\TextColumn::make('tanggal_surat')
                    ->label('Tgl Surat')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sifat')
                    ->label('Sifat')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Biasa' => 'gray',
                        'Penting' => 'warning',
                        'Rahasia' => 'danger',
                        'Segera' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Draft' => 'gray',
                        'Menunggu Persetujuan' => 'warning',
                        'Disetujui' => 'info',
                        'Terkirim' => 'success',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('sifat')
                    ->options([
                        'Biasa' => 'Biasa',
                        'Penting' => 'Penting',
                        'Rahasia' => 'Rahasia',
                        'Segera' => 'Segera',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Draft' => 'Draft',
                        'Menunggu Persetujuan' => 'Menunggu Persetujuan',
                        'Disetujui' => 'Disetujui',
                        'Terkirim' => 'Terkirim',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('cetak')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (SuratKeluar $record) => route('superadmin.suratkeluar.cetak', $record->id))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListSuratKeluars::route('/'),
            'create' => Pages\CreateSuratKeluar::route('/create'),
            'edit' => Pages\EditSuratKeluar::route('/{record}/edit'),
        ];
    }
}
