<?php

namespace App\Filament\Resources\Barangs\Schemas;

use App\Enums\KondisiBarang;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class BarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // === Informasi Barang ===
            Section::make("Informasi Barang")
                ->description("Isi informasi dasar barang inventaris")
                ->icon(Heroicon::OutlinedCube)
                ->schema([
                    TextInput::make("nama_barang")
                        ->label("Nama Barang")
                        ->placeholder("Contoh: Kursi Lipat")
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Textarea::make("keterangan")
                        ->label("Keterangan")
                        ->placeholder("Deskripsi singkat mengenai barang...")
                        ->rows(3)
                        ->nullable()
                        ->columnSpanFull(),
                ])
                ->columns(1),

            // === Stok Awal per Kondisi ===
            Section::make("Stok per Kondisi")
                ->description(
                    "Masukkan jumlah stok awal untuk masing-masing kondisi barang",
                )
                ->icon(Heroicon::OutlinedArchiveBox)
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make("stok_baik")
                            ->label("🟢 Kondisi Baik")
                            ->helperText("Jumlah barang dalam kondisi baik")
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->suffix("unit")
                            ->required(),

                        TextInput::make("stok_rusak_ringan")
                            ->label("🟠 Rusak Ringan")
                            ->helperText("Jumlah barang rusak ringan")
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->suffix("unit")
                            ->required(),

                        TextInput::make("stok_rusak_berat")
                            ->label("🔴 Rusak Berat")
                            ->helperText("Jumlah barang rusak berat")
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->suffix("unit")
                            ->required(),
                    ]),
                ]),
        ]);
    }
}
