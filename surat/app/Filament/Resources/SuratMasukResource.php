<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuratMasukResource\Pages;
use App\Models\Disposisi;
use App\Models\KategoriSurat;
use App\Models\Profil;
use App\Models\SuratMasuk;
use App\Models\SuratMasukPenerima;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class SuratMasukResource extends Resource
{
    protected static ?string $model = SuratMasuk::class;

    protected static ?string $navigationGroup = 'Persuratan';

    protected static ?string $navigationLabel = 'Surat Masuk';

    protected static ?string $modelLabel = 'Surat Masuk';

    protected static ?string $pluralModelLabel = 'Surat Masuk';

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['kategoriSurat', 'penerimaList', 'disposisi.penerima'])
            ->forUser(auth()->user());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Identitas Surat Masuk')
                            ->schema([
                                Forms\Components\TextInput::make('nomor_surat')
                                    ->label('Nomor Surat')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: 005/123/401.101/2026'),

                                Forms\Components\TextInput::make('nomor_agenda')
                                    ->label('Nomor Agenda Registrasi')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Otomatis / Format Resmi')
                                    ->helperText('Format: KODE_KLASIFIKASI/NO_URUT/KODE_INSTANSI/TAHUN'),

                                Forms\Components\TextInput::make('pengirim')
                                    ->label('Asal Pengirim / Instansi')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: Dinas Komunikasi dan Informatika'),

                                Forms\Components\Select::make('kategori_id')
                                    ->label('Kategori Klasifikasi')
                                    ->relationship('kategoriSurat', 'nama_kategori')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        if (!$state || filled($get('nomor_agenda'))) {
                                            return;
                                        }
                                        $kategori = KategoriSurat::find($state);
                                        $profil = Profil::first();
                                        $kodeInstansi = $profil->kode_lembaga ?? 'KEC.LMG';
                                        $tahun = date('Y');
                                        $kodeSurat = $kategori->kode_kategori ?? '400';

                                        $lastSurat = SuratMasuk::whereYear('created_at', $tahun)->latest('id')->first();
                                        $nextNum = 1;
                                        if ($lastSurat && $lastSurat->nomor_agenda) {
                                            $parts = explode('/', $lastSurat->nomor_agenda);
                                            $nextNum = ((int) ($parts[1] ?? 0)) + 1;
                                        }

                                        $agenda = sprintf('%s/%03d/%s/%d', $kodeSurat, $nextNum, $kodeInstansi, $tahun);
                                        $set('nomor_agenda', $agenda);
                                    }),

                                Forms\Components\DatePicker::make('tanggal_surat')
                                    ->label('Tanggal Surat')
                                    ->default(now())
                                    ->required(),

                                Forms\Components\DatePicker::make('tanggal_diterima')
                                    ->label('Tanggal Diterima')
                                    ->default(now())
                                    ->required(),

                                Forms\Components\Textarea::make('perihal')
                                    ->label('Perihal / Ringkasan Surat')
                                    ->required()
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Section::make('Tujuan Penerima (7 Jabatan)')
                            ->description('Pilih satu atau beberapa jabatan penerima surat kedinasan ini.')
                            ->schema([
                                Forms\Components\CheckboxList::make('penerima_jabatan')
                                    ->label('Daftar Jabatan Dituju')
                                    ->options([
                                        'Sekretaris Kecamatan' => 'Sekretaris Kecamatan',
                                        'Kepala Seksi Pemerintahan' => 'Kepala Seksi Pemerintahan',
                                        'Kepala Seksi Pelayanan Publik' => 'Kepala Seksi Pelayanan Publik',
                                        'Kepala Seksi Ketentraman dan Ketertiban Umum' => 'Kepala Seksi Ketentraman dan Ketertiban Umum',
                                        'Kepala Seksi Pembangunan dan Pemberdayaan Masyarakat' => 'Kepala Seksi Pembangunan dan Pemberdayaan Masyarakat',
                                        'Kepala Sub Bagian Umum dan Kepegawaian' => 'Kepala Sub Bagian Umum dan Kepegawaian',
                                        'Kepala Sub Bagian Perencanaan Evaluasi dan Keuangan' => 'Kepala Sub Bagian Perencanaan Evaluasi dan Keuangan',
                                    ])
                                    ->columns(2)
                                    ->afterStateHydrated(function (Forms\Components\CheckboxList $component, ?SuratMasuk $record) {
                                        if (!$record) return;
                                        $jabatanNames = $record->penerimaList->pluck('jabatan')->toArray();
                                        if (empty($jabatanNames) && !empty($record->penerima_dituju)) {
                                            $jabatanNames = array_map('trim', explode(',', $record->penerima_dituju));
                                        }
                                        $component->state($jabatanNames);
                                    })
                                    ->dehydrated(false),
                            ]),
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Sifat & Berkas Dokumen')
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
                                    ->label('Status Pemrosesan')
                                    ->options([
                                        'Diarsipkan' => 'Diarsipkan',
                                        'Proses Disposisi' => 'Proses Disposisi',
                                        'Selesai' => 'Selesai',
                                    ])
                                    ->default('Proses Disposisi')
                                    ->required(),

                                Forms\Components\FileUpload::make('file_path')
                                    ->label('Lampiran Berkas Surat')
                                    ->disk('public')
                                    ->directory('suratmasuk')
                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'])
                                    ->maxSize(10240)
                                    ->required()
                                    ->helperText('PDF, DOCX, JPG atau PNG, Maks. 10MB.')
                                    ->openable()
                                    ->downloadable(),

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
                Tables\Columns\TextColumn::make('nomor_agenda')
                    ->label('No. Agenda')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('pengirim')
                    ->label('Pengirim')
                    ->searchable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('perihal')
                    ->label('Perihal')
                    ->searchable()
                    ->limit(35)
                    ->tooltip(fn (SuratMasuk $record): string => $record->perihal ?? ''),

                Tables\Columns\TextColumn::make('tanggal_surat')
                    ->label('Tgl Surat')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('penerima_dituju')
                    ->label('Penerima')
                    ->placeholder('-')
                    ->limit(25)
                    ->badge()
                    ->color('info')
                    ->tooltip(function (SuratMasuk $record): string {
                        $penerimas = $record->penerimaList->pluck('jabatan')->toArray();
                        return !empty($penerimas) ? implode("\n", $penerimas) : ($record->penerima_dituju ?? '-');
                    }),

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
                        'Diarsipkan' => 'gray',
                        'Proses Disposisi' => 'warning',
                        'Selesai' => 'success',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('kategori_id')
                    ->label('Kategori Klasifikasi')
                    ->relationship('kategoriSurat', 'nama_kategori'),

                Tables\Filters\SelectFilter::make('sifat')
                    ->label('Sifat Surat')
                    ->options([
                        'Biasa' => 'Biasa',
                        'Penting' => 'Penting',
                        'Rahasia' => 'Rahasia',
                        'Segera' => 'Segera',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Surat')
                    ->options([
                        'Diarsipkan' => 'Diarsipkan',
                        'Proses Disposisi' => 'Proses Disposisi',
                        'Selesai' => 'Selesai',
                    ]),
            ])
            ->actions([
                // Aksi Lihat Detail & Unduh Berkas
                Tables\Actions\ViewAction::make(),

                // Aksi Tindak Lanjut Khusus untuk Pengguna Jabatan
                Tables\Actions\Action::make('tindakLanjut')
                    ->label('Tindak Lanjut')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn () => auth()->user()?->isJabatan() ?? false)
                    ->form([
                        Forms\Components\Select::make('status_penerima')
                            ->label('Status Tindak Lanjut Jabatan')
                            ->options([
                                'Dibaca' => 'Sudah Dibaca',
                                'Diproses' => 'Sedang Diproses',
                                'Selesai' => 'Selesai Ditindaklanjuti',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('catatan_tindak_lanjut')
                            ->label('Catatan / Respon Tindak Lanjut')
                            ->rows(3)
                            ->placeholder('Tuliskan respon, saran, atau hasil koordinasi...'),
                    ])
                    ->mountUsing(function (Forms\ComponentContainer $form, SuratMasuk $record) {
                        $user = auth()->user();
                        $penerima = SuratMasukPenerima::where('surat_masuk_id', $record->id)
                            ->where(function ($q) use ($user) {
                                $q->where('user_id', $user->id)
                                  ->orWhere('jabatan', $user->jabatan);
                            })->first();

                        if ($penerima) {
                            $form->fill([
                                'status_penerima' => $penerima->status === 'Terkirim' ? 'Dibaca' : $penerima->status,
                                'catatan_tindak_lanjut' => $penerima->catatan,
                            ]);
                        }
                    })
                    ->action(function (SuratMasuk $record, array $data) {
                        $user = auth()->user();
                        $penerima = SuratMasukPenerima::firstOrNew([
                            'surat_masuk_id' => $record->id,
                            'jabatan' => $user->jabatan ?: $user->name,
                        ]);
                        $penerima->user_id = $user->id;
                        $penerima->status = $data['status_penerima'];
                        $penerima->catatan = $data['catatan_tindak_lanjut'] ?? null;
                        if ($data['status_penerima'] === 'Selesai') {
                            $penerima->selesai_pada = now();
                        }
                        $penerima->save();

                        // Update juga catatan di tabel disposisi jika ada relasi
                        $disposisi = Disposisi::where('surat_masuk_id', $record->id)
                            ->where('penerima_id', $user->id)
                            ->first();
                        if ($disposisi) {
                            $disposisi->status = $data['status_penerima'] === 'Selesai' ? 'Selesai' : 'Proses';
                            if (!empty($data['catatan_tindak_lanjut'])) {
                                $disposisi->catatan_tindak_lanjut = $data['catatan_tindak_lanjut'];
                            }
                            $disposisi->save();
                        }

                        Notification::make()
                            ->success()
                            ->title('Tindak lanjut berhasil disimpan')
                            ->send();
                    }),

                // Aksi Buat Disposisi untuk Pimpinan / Admin
                Tables\Actions\Action::make('buatDisposisi')
                    ->label('Disposisi')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn () => auth()->user()?->isAdmin() ?? false)
                    ->form([
                        Forms\Components\Select::make('penerima_id')
                            ->label('Jabatan Penerima Disposisi')
                            ->options(function () {
                                return User::whereNotNull('jabatan')
                                    ->pluck('jabatan', 'id');
                            })
                            ->searchable()
                            ->required(),
                        Forms\Components\Textarea::make('isi_disposisi')
                            ->label('Petunjuk / Instruksi Disposisi')
                            ->required()
                            ->rows(3)
                            ->placeholder('Tuliskan petunjuk tindak lanjut surat...'),
                        Forms\Components\Select::make('sifat_instruksi')
                            ->label('Sifat Instruksi')
                            ->options([
                                'Biasa' => 'Biasa',
                                'Penting' => 'Penting',
                                'Sangat Segera' => 'Sangat Segera',
                            ])
                            ->default('Biasa')
                            ->required(),
                        Forms\Components\DatePicker::make('batas_waktu')
                            ->label('Batas Waktu'),
                    ])
                    ->action(function (SuratMasuk $record, array $data) {
                        Disposisi::create([
                            'surat_masuk_id' => $record->id,
                            'pengirim_id' => auth()->id(),
                            'penerima_id' => $data['penerima_id'],
                            'isi_disposisi' => $data['isi_disposisi'],
                            'sifat_instruksi' => $data['sifat_instruksi'],
                            'batas_waktu' => $data['batas_waktu'] ?? null,
                            'status' => 'Belum Dibaca',
                        ]);

                        $record->update(['status' => 'Proses Disposisi']);

                        Notification::make()
                            ->success()
                            ->title('Disposisi Berhasil Diterbitkan')
                            ->send();
                    }),

                // Cetak Lembar Disposisi
                Tables\Actions\Action::make('cetakDisposisi')
                    ->label('Cetak Lembar')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->visible(fn (SuratMasuk $record) => $record->disposisi !== null)
                    ->url(fn (SuratMasuk $record) => route('superadmin.desposisi.cetak', $record->disposisi->id))
                    ->openUrlInNewTab(),

                // Unduh File Berkas Asli
                Tables\Actions\Action::make('unduhFile')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->url(fn (SuratMasuk $record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->isAdmin() ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->isAdmin() ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->isAdmin() ?? false),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuratMasuks::route('/'),
            'create' => Pages\CreateSuratMasuk::route('/create'),
            'edit' => Pages\EditSuratMasuk::route('/{record}/edit'),
        ];
    }
}
