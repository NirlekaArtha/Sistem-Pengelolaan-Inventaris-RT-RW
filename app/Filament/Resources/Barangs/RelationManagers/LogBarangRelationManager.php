<?php

namespace App\Filament\Resources\Barangs\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LogBarangRelationManager extends RelationManager
{
    protected static string $relationship = "logBarang";

    protected static ?string $title = "Riwayat Log Barang";

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute("tipe")
            ->defaultSort("created_at", "desc")
            ->columns([
                TextColumn::make("created_at")
                    ->label("Waktu")
                    ->dateTime("d M Y, H:i")
                    ->sortable()
                    ->color("gray"),

                TextColumn::make("tipe")
                    ->label("Tipe")
                    ->badge()
                    ->icon(
                        fn($state) => match ($state?->value ?? $state) {
                            "masuk" => Heroicon::OutlinedArrowDownLeft,
                            "keluar" => Heroicon::OutlinedArrowUpRight,
                            default
                                => null, // Berikan null jika tidak ada ikon yang cocok
                        },
                    )
                    ->formatStateUsing(
                        fn($state): string => match (
                            $state instanceof \BackedEnum
                                ? $state->value
                                : $state
                        ) {
                            "masuk" => " Masuk",
                            "keluar" => " Keluar",
                            default => $state,
                        },
                    )
                    ->color(
                        fn($state): string => match (
                            $state instanceof \BackedEnum
                                ? $state->value
                                : $state
                        ) {
                            "masuk" => "success",
                            "keluar" => "danger",
                            default => "gray",
                        },
                    ),

                TextColumn::make("kondisi")
                    ->label("Kondisi")
                    ->badge()
                    ->formatStateUsing(
                        fn($state): string => match (
                            $state instanceof \BackedEnum
                                ? $state->value
                                : $state
                        ) {
                            "baik" => "🟢 Baik",
                            "rusak_ringan" => "🟠 Rusak Ringan",
                            "rusak_berat" => "🔴 Rusak Berat",
                            default => $state,
                        },
                    )
                    ->color(
                        fn($state): string => match (
                            $state instanceof \BackedEnum
                                ? $state->value
                                : $state
                        ) {
                            "baik" => "success",
                            "rusak_ringan" => "warning",
                            "rusak_berat" => "danger",
                            default => "gray",
                        },
                    ),

                TextColumn::make("jumlah")
                    ->label("Jumlah")
                    ->numeric()
                    ->badge()
                    ->color(
                        fn($state, $record): string => match (
                            $record->tipe instanceof \BackedEnum
                                ? $record->tipe->value
                                : $record->tipe
                        ) {
                            "masuk" => "success",
                            "keluar" => "danger",
                            default => "gray",
                        },
                    )
                    ->suffix(" unit"),

                TextColumn::make("keterangan")
                    ->label("Keterangan")
                    ->placeholder("—")
                    ->wrap()
                    ->color("gray"),
            ])
            ->filters([
                SelectFilter::make("tipe")
                    ->label("Tipe")
                    ->options([
                        "masuk" => "Masuk",
                        "keluar" => "Keluar",
                    ]),

                SelectFilter::make("kondisi")
                    ->label("Kondisi")
                    ->options([
                        "baik" => "Baik",
                        "rusak_ringan" => "Rusak Ringan",
                        "rusak_berat" => "Rusak Berat",
                    ]),
            ])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([])
            ->emptyStateHeading("Belum ada log")
            ->emptyStateDescription(
                "Log akan otomatis tercatat saat stok barang berubah.",
            );
    }
}
