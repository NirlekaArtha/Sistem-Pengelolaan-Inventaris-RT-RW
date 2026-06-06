<?php

namespace App\Filament\Resources\Barangs\Schemas;

use App\Enums\KondisiBarang;
use App\Models\Barang;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

class BarangInfolist
{

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // === Informasi Barang ===
            Section::make("Informasi Barang")
                ->description("Detail lengkap barang inventaris RT/RW")
                ->icon(Heroicon::OutlinedCube)
                ->columnSpanFull()
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make("nama_barang")
                            ->label("Nama Barang")
                            ->icon(Heroicon::OutlinedTag)
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold)
                            ->columnSpan(2),

                        TextEntry::make("keterangan")
                            ->label("Keterangan")
                            ->icon(Heroicon::OutlinedDocumentText)
                            ->placeholder("Tidak ada keterangan")
                            ->columnSpan(2),

                        TextEntry::make("jumlah_total")
                            ->label("Total Stok Keseluruhan")
                            ->icon(Heroicon::OutlinedArchiveBox)
                            ->badge()
                            ->color("info")
                            ->suffix(" unit"),

                        TextEntry::make("created_at")
                            ->label("Tanggal Dicatat")
                            ->icon(Heroicon::OutlinedCalendar)
                            ->dateTime("d M Y, H:i"),
                    ]),
                ]),
        ]);
    }
}
