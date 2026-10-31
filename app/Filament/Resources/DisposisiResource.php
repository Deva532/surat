<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DisposisiResource\Pages;
use App\Models\Disposisi;
use App\Models\DisposisiPenerima;
use App\Models\SuratMasuk;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DisposisiResource extends Resource
{
    protected static ?string $model = Disposisi::class;

    protected static ?string $navigationGroup = 'Persuratan';

    protected static ?string $navigationLabel = 'Disposisi Surat';

    protected static ?string $modelLabel = 'Disposisi';

    protected static ?string $pluralModelLabel = 'Disposisi Surat';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['suratMasuk', 'pengirim', 'penerima', 'penerimaList']);

        $user = auth()->user();

        if ($user && !$user->isAdmin()) {
            // Jabatan hanya melihat disposisi yang ditujukan kepada mereka (via JSON atau pivot)
            return $query->where(function ($q) use ($user) {
                $q->whereJsonContains('penerima_jabatan', $user->jabatan)
                  ->orWhere('penerima_id', $user->id);
            });
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Surat Masuk Terkait')
                    ->schema([
                        Forms\Components\Select::make('surat_masuk_id')
                            ->label('Surat Masuk')
                            ->options(function () {
                                return SuratMasuk::latest()
                                    ->get()
                                    ->mapWithKeys(function ($s) {
                                        return [$s->id => "{$s->nomor_surat} — {$s->pengirim} ({$s->perihal})"];
                                    });
                            })
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Pengaturan Disposisi')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_disposisi')
                            ->label('Tanggal Disposisi')
                            ->default(now())
                            ->required(),

                        Forms\Components\Select::make('sifat_instruksi')
                            ->label('Sifat Instruksi')
                            ->options([
                                'Biasa'         => 'Biasa',
                                'Penting'       => 'Penting',
                                'Sangat Segera' => 'Sangat Segera',
                            ])
                            ->default('Biasa')
                            ->required(),

                        Forms\Components\DatePicker::make('batas_waktu')
                            ->label('Batas Waktu Penyelesaian')
                            ->nullable(),

                        Forms\Components\Select::make('status')
                            ->label('Status Disposisi')
                            ->options([
                                'Belum Dibaca' => 'Belum Dibaca',
                                'Proses'       => 'Sedang Diproses',
                                'Selesai'      => 'Selesai',
                            ])
                            ->default('Belum Dibaca')
                            ->required(),

                        Forms\Components\CheckboxList::make('penerima_jabatan')
                            ->label('Diteruskan kepada Sdr. (7 Jabatan)')
                            ->options([
                                'Sekretaris Kecamatan'                                        => 'Sekretaris Kecamatan',
                                'Kepala Seksi Pemerintahan'                                   => 'Kepala Seksi Pemerintahan',
                                'Kepala Seksi Pelayanan Publik'                               => 'Kepala Seksi Pelayanan Publik',
                                'Kepala Seksi Ketentraman dan Ketertiban Umum'                => 'Kepala Seksi Ketentraman dan Ketertiban Umum',
                                'Kepala Seksi Pembangunan dan Pemberdayaan Masyarakat'        => 'Kepala Seksi Pembangunan dan Pemberdayaan Masyarakat',
                                'Kepala Sub Bagian Umum dan Kepegawaian'                      => 'Kepala Sub Bagian Umum dan Kepegawaian',
                                'Kepala Sub Bagian Perencanaan Evaluasi dan Keuangan'         => 'Kepala Sub Bagian Perencanaan Evaluasi dan Keuangan',
                            ])
                            ->columns(2)
                            ->columnSpanFull(),

                        Forms\Components\CheckboxList::make('instruksi')
                            ->label('Dengan hormat harap (Instruksi Resmi)')
                            ->options([
                                'Tanggapan dan Saran'      => 'Tanggapan dan Saran',
                                'Proses Lebih Lanjut'      => 'Proses Lebih Lanjut',
                                'Koordinasi/Konfirmasikan' => 'Koordinasi/Konfirmasikan',
                                'Lain-lain'                => 'Lain-lain',
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->live(),

                        Forms\Components\Textarea::make('instruksi_lainnya')
                            ->label('Keterangan Instruksi Lainnya')
                            ->rows(2)
                            ->placeholder('Tuliskan detail instruksi lainnya...')
                            ->columnSpanFull()
                            ->visible(fn (Forms\Get $get) => in_array('Lain-lain', (array) $get('instruksi'))),

                        Forms\Components\Textarea::make('isi_disposisi')
                            ->label('Catatan / Instruksi Tertulis Pimpinan')
                            ->rows(3)
                            ->placeholder('Tuliskan catatan atau petunjuk pimpinan...')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('catatan_tindak_lanjut')
                            ->label('Catatan Tindak Lanjut Penerima')
                            ->rows(2)
                            ->columnSpanFull()
                            ->placeholder('Respon atau laporan penyelesaian...'),

                        Forms\Components\Hidden::make('pengirim_id')
                            ->default(fn () => auth()->id()),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('suratMasuk.nomor_surat')
                    ->label('No. Surat Masuk')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('suratMasuk.perihal')
                    ->label('Perihal')
                    ->limit(30)
                    ->searchable(),

                Tables\Columns\TextColumn::make('penerima_jabatan')
                    ->label('Penerima Disposisi')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(function ($state, Disposisi $record) {
                        $jabatan = $record->penerima_jabatan ?? [];
                        if (empty($jabatan)) {
                            return $record->penerima?->jabatan ?? $record->penerima?->name ?? '-';
                        }
                        return count($jabatan) . ' Jabatan';
                    })
                    ->tooltip(function (Disposisi $record): string {
                        $jabatan = $record->penerima_jabatan ?? [];
                        return !empty($jabatan) ? implode("\n", $jabatan) : '';
                    }),

                Tables\Columns\TextColumn::make('isi_disposisi')
                    ->label('Instruksi')
                    ->limit(35)
                    ->tooltip(fn (Disposisi $record): string => $record->isi_disposisi ?? ''),

                Tables\Columns\TextColumn::make('sifat_instruksi')
                    ->label('Sifat')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Biasa'         => 'gray',
                        'Penting'       => 'warning',
                        'Sangat Segera' => 'danger',
                        default         => 'gray',
                    }),

                Tables\Columns\TextColumn::make('tanggal_disposisi')
                    ->label('Tgl Disposisi')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('batas_waktu')
                    ->label('Batas Waktu')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Belum Dibaca' => 'gray',
                        'Proses'       => 'warning',
                        'Selesai'      => 'success',
                        default        => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Belum Dibaca' => 'Belum Dibaca',
                        'Proses'       => 'Sedang Diproses',
                        'Selesai'      => 'Selesai',
                    ]),
                Tables\Filters\SelectFilter::make('sifat_instruksi')
                    ->options([
                        'Biasa'         => 'Biasa',
                        'Penting'       => 'Penting',
                        'Sangat Segera' => 'Sangat Segera',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('tindakLanjut')
                    ->label('Tindak Lanjut')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status Penyelesaian')
                            ->options([
                                'Proses'  => 'Sedang Diproses',
                                'Selesai' => 'Selesai',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('catatan_tindak_lanjut')
                            ->label('Catatan Tindak Lanjut')
                            ->rows(3)
                            ->placeholder('Hasil penanganan atau tindak lanjut disposisi...'),
                    ])
                    ->mountUsing(fn (Forms\ComponentContainer $form, Disposisi $record) => $form->fill([
                        'status'                => $record->status === 'Belum Dibaca' ? 'Proses' : $record->status,
                        'catatan_tindak_lanjut' => $record->catatan_tindak_lanjut,
                    ]))
                    ->action(function (Disposisi $record, array $data) {
                        $record->update([
                            'status'                => $data['status'],
                            'catatan_tindak_lanjut' => $data['catatan_tindak_lanjut'] ?? $record->catatan_tindak_lanjut,
                        ]);

                        // Juga update status di disposisi_penerima untuk jabatan ini
                        $user = auth()->user();
                        if ($user?->jabatan) {
                            DisposisiPenerima::where('disposisi_id', $record->id)
                                ->where(function ($q) use ($user) {
                                    $q->where('user_id', $user->id)
                                      ->orWhere('jabatan', $user->jabatan);
                                })
                                ->update([
                                    'status'                => $data['status'],
                                    'catatan_tindak_lanjut' => $data['catatan_tindak_lanjut'] ?? null,
                                    'selesai_pada'          => $data['status'] === 'Selesai' ? now() : null,
                                ]);
                        }

                        Notification::make()
                            ->success()
                            ->title('Status disposisi diperbarui')
                            ->send();
                    }),

                Tables\Actions\Action::make('cetak')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (Disposisi $record) => route('superadmin.desposisi.cetak', $record->id))
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
            'index'  => Pages\ListDisposisis::route('/'),
            'create' => Pages\CreateDisposisi::route('/create'),
            'edit'   => Pages\EditDisposisi::route('/{record}/edit'),
        ];
    }
}
