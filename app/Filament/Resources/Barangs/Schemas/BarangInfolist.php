<?php

namespace App\Filament\Resources\Barangs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BarangInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nama_barang'),
                TextEntry::make('jumlah_total')
                    ->numeric(),
                TextEntry::make('jumlah_tersedia')
                    ->numeric(),
                TextEntry::make('kondisi')
                    ->badge(),
                TextEntry::make('lokasi_penyimpanan'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
