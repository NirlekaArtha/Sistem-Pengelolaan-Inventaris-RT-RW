<?php

namespace App\Filament\Resources\Barangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("nama_barang")->searchable(),
                TextColumn::make("jumlah_total")->numeric()->sortable(),
                TextColumn::make("jumlah_tersedia")->numeric()->sortable(),
                TextColumn::make("kondisi")
                    ->badge()
                    ->colors([
                        "success" => "baik",
                        "warning" => "rusak ringan",
                        "danger" => "rusak berat",
                    ]),
                TextColumn::make("lokasi_penyimpanan")->searchable(),
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
                //
            ])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
