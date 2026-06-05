<?php

namespace App\Filament\Resources\LogBarangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LogBarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("barang.nama_barang")
                    ->label("Nama barang")
                    ->searchable()
                    ->sortable(),
                TextColumn::make("admin.name")
                    ->label("Admin")
                    ->searchable()
                    ->sortable(),
                TextColumn::make("tipe")
                    ->badge()
                    ->badge()
                    ->colors([
                        "success" => "masuk",
                        "warning" => "keluar",
                    ]),
                TextColumn::make("jumlah")->numeric()->sortable(),
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
