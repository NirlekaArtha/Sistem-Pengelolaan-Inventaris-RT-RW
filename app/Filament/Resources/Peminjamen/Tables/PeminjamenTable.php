<?php

namespace App\Filament\Resources\Peminjamen\Tables;

use App\Models\Peminjaman;
use App\Enums\StatusPeminjaman;
use App\Services\PengembalianService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class PeminjamenTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("warga.nama")
                    ->label("Nama Warga")
                    ->searchable()
                    ->sortable(),
                TextColumn::make("admin.nama")
                    ->label("Admin")
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make("tanggal_pinjam")
                    ->label("Tanggal Pinjam")
                    ->date()
                    ->sortable(),
                TextColumn::make("tenggat_pengembalian")
                    ->label("Tenggat Pengembalian")
                    ->date()
                    ->sortable(),
                TextColumn::make("tanggal_kembali")
                    ->label("Tanggal Kembali")
                    ->date()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make("status")
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof \BackedEnum ? $state->value : $state) {
                        'dikembalikan' => 'success',
                        'dipinjam' => 'warning',
                        'terlambat' => 'danger',
                        'dikembalikan_terlambat' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make("created_at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make("updated_at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'dipinjam' => 'Dipinjam',
                        'dikembalikan' => 'Dikembalikan',
                        'terlambat' => 'Terlambat',
                        'dikembalikan_terlambat' => 'Dikembalikan Terlambat',
                    ])
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('proses_kembali')
                    ->label('Proses Kembali')
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->color('success')
                    ->visible(fn (Peminjaman $record) => in_array($record->status->value ?? $record->status, ['dipinjam', 'terlambat']))
                    ->mountUsing(function (Schema $schema, Peminjaman $record) {
                        $data = [];
                        foreach ($record->detailPeminjaman as $detail) {
                            // AMAN: Mengambil ->value dari Enum (berupa string seperti 'baik' atau 'rusak_ringan')
                            $kondisiPinjamRaw = $detail->stokBarang->kondisi;
                            $kondisiPinjamValue = $kondisiPinjamRaw instanceof \BackedEnum ? $kondisiPinjamRaw->value : $kondisiPinjamRaw;

                            $data['items'][] = [
                                'detail_id' => $detail->id,
                                'nama_barang' => $detail->stokBarang->barang->nama_barang,
                                'kondisi_pinjam' => $kondisiPinjamValue,
                                'jumlah_pinjam' => $detail->jumlah,
                                'baik' => 0,
                                'rusak_ringan' => 0,
                                'rusak_berat' => 0,
                            ];
                        }
                        $schema->fill($data);
                    })
                    ->form(fn (Schema $schema) => $schema->components([
                        Repeater::make('items')
                            ->label('Barang yang Dikembalikan')
                            ->schema(fn (Schema $innerSchema) => $innerSchema->components([
                                Hidden::make('detail_id'),
                                Hidden::make('nama_barang'),
                                Hidden::make('kondisi_pinjam'),
                                Hidden::make('jumlah_pinjam'),
                                Placeholder::make('barang_info')
                                    ->label('Barang & Kondisi Pinjam')
                                    ->content(function (callable $get) {
                                        $kondisi = $get('kondisi_pinjam');
                                        
                                        // Jika Enum, panggil method getLabel() yang sudah kamu buat
                                        $labelKondisi = $kondisi instanceof \App\Enums\KondisiBarang 
                                            ? $kondisi->getLabel() 
                                            : ucfirst(str_replace('_', ' ', (string) $kondisi));

                                        return "{$get('nama_barang')} ({$labelKondisi}) - Pinjam: {$get('jumlah_pinjam')}";
                                    }),
                                TextInput::make('baik')
                                    ->label('Jumlah Kembali Baik')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->required(),
                                TextInput::make('rusak_ringan')
                                    ->label('Jumlah Kembali Rusak Ringan')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->required(),
                                TextInput::make('rusak_berat')
                                    ->label('Jumlah Kembali Rusak Berat')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->required(),
                            ]))
                            ->columns(4)
                            ->addable(false)
                            ->deletable(false)
                    ]))
                    ->action(function (Peminjaman $record, array $data) {
                        $kondisiKembali = [];
                        foreach ($data['items'] ?? [] as $key => $item) {
                            $baik = (int) ($item['baik'] ?? 0);
                            $rusakRingan = (int) ($item['rusak_ringan'] ?? 0);
                            $rusakBerat = (int) ($item['rusak_berat'] ?? 0);
                            $totalKembali = $baik + $rusakRingan + $rusakBerat;
                            $jumlahPinjam = (int) ($item['jumlah_pinjam'] ?? 0);

                            if ($totalKembali > $jumlahPinjam) {
                                throw ValidationException::withMessages([
                                    "items.{$key}.baik" => "Total kembali tidak boleh melebihi jumlah pinjam ({$jumlahPinjam}).",
                                ]);
                            }

                            $detailId = $item['detail_id'];
                            $kondisiKembali[$detailId] = [
                                'baik' => $baik,
                                'rusak_ringan' => $rusakRingan,
                                'rusak_berat' => $rusakBerat,
                            ];
                        }

                        /** @var PengembalianService $pengembalianService */
                        $pengembalianService = app(PengembalianService::class);
                        $pengembalianService->prosesKembali($record, $kondisiKembali);

                        Notification::make()
                            ->title('Pengembalian Berhasil')
                            ->success()
                            ->send();
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}