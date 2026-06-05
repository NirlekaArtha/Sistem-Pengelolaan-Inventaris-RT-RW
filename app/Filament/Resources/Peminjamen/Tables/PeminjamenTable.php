<?php

namespace App\Filament\Resources\Peminjamen\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
                TextColumn::make("admin.name")
                    ->label("Admin")
                    ->searchable()
                    ->sortable(),
                TextColumn::make("tanggal_pinjam")->date()->sortable(),
                TextColumn::make("tenggat_pengembalian")->date()->sortable(),
                TextColumn::make("tanggal_kembali")->date()->sortable(),
                TextColumn::make("status")
                    ->badge()
                    ->badge()
                    ->colors([
                        "success" => "dikembalikan",
                        "warning" => "dipinjam",
                        "danger" => "terlambat",
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
