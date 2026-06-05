<?php

namespace App\Filament\Resources\DetailPeminjamen\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DetailPeminjamenTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("peminjaman_id")->numeric()->sortable(),
                TextColumn::make("barang.nama_barang")
                    ->label("Nama barang")
                    ->searchable()
                    ->sortable(),
                TextColumn::make("jumlah")->numeric()->sortable(),
                TextColumn::make("kondisi_saat_pinjam")
                    ->badge()
                    ->badge()
                    ->colors([
                        "success" => "baik",
                        "warning" => "rusak ringan",
                        "danger" => "rusak berat",
                    ]),
                TextColumn::make("kondisi_saat_kembali")
                    ->badge()
                    ->badge()
                    ->colors([
                        "success" => "baik",
                        "warning" => "rusak ringan",
                        "danger" => "rusak berat",
                    ]),
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
