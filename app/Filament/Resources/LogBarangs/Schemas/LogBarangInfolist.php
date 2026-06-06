<?php

namespace App\Filament\Resources\LogBarangs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

class LogBarangInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // === Informasi Barang ===
            Section::make('Informasi Barang')
                ->description('Detail barang yang tercatat dalam log ini')
                ->icon(Heroicon::OutlinedCube)
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('barang.nama_barang')
                            ->label('Nama Barang')
                            ->icon(Heroicon::OutlinedArchiveBox)
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large)
                            ->columnSpan(2),

                        TextEntry::make('kondisi')
                            ->label('Kondisi Barang')
                            ->icon(Heroicon::OutlinedTag)
                            ->badge()
                            ->color(
                                fn ($state): string => match (
                                    $state instanceof \BackedEnum
                                        ? $state->value
                                        : (string) $state
                                ) {
                                    'baik'         => 'success',
                                    'rusak_ringan' => 'warning',
                                    'rusak_berat'  => 'danger',
                                    default        => 'gray',
                                },
                            ),

                        TextEntry::make('tipe')
                            ->label('Tipe Log')
                            ->icon(Heroicon::OutlinedArrowsRightLeft)
                            ->badge()
                            ->color(
                                fn ($state): string => match (
                                    $state instanceof \BackedEnum
                                        ? $state->value
                                        : (string) $state
                                ) {
                                    'masuk'  => 'success',
                                    'keluar' => 'danger',
                                    default  => 'gray',
                                },
                            ),
                    ]),
                ]),

            // === Detail Pergerakan ===
            Section::make('Detail Pergerakan')
                ->description('Rincian jumlah dan keterangan log barang')
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('jumlah')
                            ->label('Jumlah')
                            ->icon(Heroicon::OutlinedHashtag)
                            ->numeric()
                            ->suffix(' unit')
                            ->badge()
                            ->color('info')
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold),

                        TextEntry::make('created_at')
                            ->label('Waktu Dicatat')
                            ->icon(Heroicon::OutlinedClock)
                            ->dateTime('d M Y, H:i')
                            ->placeholder('—'),
                    ]),

                    TextEntry::make('keterangan')
                        ->label('Keterangan')
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->placeholder('Tidak ada keterangan')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
