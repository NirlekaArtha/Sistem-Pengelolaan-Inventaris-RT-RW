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
                    ->badge(),

                TextColumn::make('jumlah_total')
                    ->label('Total Fisik')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->suffix(' unit')
                    ->tooltip('Total unit yang dimiliki (termasuk yang sedang dipinjam)'),

                TextColumn::make('stok_tersedia')
                    ->label('Stok Tersedia')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state <= 0  => 'danger',
                        $state <= 3  => 'warning',
                        default      => 'success',
                    })
                    ->suffix(' unit')
                    ->tooltip('Unit yang tersedia untuk dipinjam saat ini'),

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
