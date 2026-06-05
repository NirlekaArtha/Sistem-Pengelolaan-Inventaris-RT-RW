<?php

namespace App\Filament\Resources\DetailPeminjamen\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DetailPeminjamanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('peminjaman_id')
                    ->numeric(),
                TextEntry::make('barang_id')
                    ->numeric(),
                TextEntry::make('jumlah')
                    ->numeric(),
                TextEntry::make('kondisi_saat_pinjam')
                    ->badge(),
                TextEntry::make('kondisi_saat_kembali')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
