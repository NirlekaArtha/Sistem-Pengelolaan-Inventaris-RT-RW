<?php

namespace App\Filament\Resources\Barangs\Tables;

use App\Enums\KondisiBarang;
use App\Models\Barang;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->description(fn (Barang $record): string => $record->keterangan ?? '-'),

                TextColumn::make('jumlah_total')
                    ->label('Total Stok')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                TextColumn::make('stok_baik')
                    ->label('🟢 Baik')
                    ->getStateUsing(fn (Barang $record) =>
                        $record->stokBarang()->where('kondisi', KondisiBarang::BAIK)->value('jumlah') ?? 0
                    )
                    ->numeric()
                    ->badge()
                    ->color('success')
                    ->alignCenter(),

                TextColumn::make('stok_rusak_ringan')
                    ->label('🟠 Rusak Ringan')
                    ->getStateUsing(fn (Barang $record) =>
                        $record->stokBarang()->where('kondisi', KondisiBarang::RUSAK_RINGAN)->value('jumlah') ?? 0
                    )
                    ->numeric()
                    ->badge()
                    ->color('warning')
                    ->alignCenter(),

                TextColumn::make('stok_rusak_berat')
                    ->label('🔴 Rusak Berat')
                    ->getStateUsing(fn (Barang $record) =>
                        $record->stokBarang()->where('kondisi', KondisiBarang::RUSAK_BERAT)->value('jumlah') ?? 0
                    )
                    ->numeric()
                    ->badge()
                    ->color('danger')
                    ->alignCenter(),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->since()
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-archive-box')
            ->emptyStateHeading('Belum ada barang')
            ->emptyStateDescription('Tambahkan barang inventaris baru dengan menekan tombol "Tambah Barang".');
    }
}
