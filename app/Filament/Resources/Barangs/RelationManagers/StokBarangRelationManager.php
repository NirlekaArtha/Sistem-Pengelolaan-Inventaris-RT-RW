<?php

namespace App\Filament\Resources\Barangs\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StokBarangRelationManager extends RelationManager
{
    protected static string $relationship = 'stokBarang';

    protected static ?string $title = 'Rincian Stok per Kondisi';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('kondisi')
            ->columns([
                TextColumn::make('kondisi')
                    ->label('Kondisi Barang')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => match (
                        $state instanceof \BackedEnum ? $state->value : $state
                    ) {
                        'baik'         => '🟢 Baik',
                        'rusak_ringan' => '🟠 Rusak Ringan',
                        'rusak_berat'  => '🔴 Rusak Berat',
                        default        => $state,
                    })
                    ->color(fn ($state): string => match (
                        $state instanceof \BackedEnum ? $state->value : $state
                    ) {
                        'baik'         => 'success',
                        'rusak_ringan' => 'warning',
                        'rusak_berat'  => 'danger',
                        default        => 'gray',
                    }),

                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->suffix(' unit'),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->color('gray'),
            ])
            ->filters([])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([])
            ->emptyStateHeading('Stok belum tersedia')
            ->emptyStateDescription('Stok akan otomatis muncul saat barang disimpan.');
    }
}
