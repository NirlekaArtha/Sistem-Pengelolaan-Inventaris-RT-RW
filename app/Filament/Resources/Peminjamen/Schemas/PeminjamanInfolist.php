<?php

namespace App\Filament\Resources\Peminjamen\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PeminjamanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('warga_id')
                    ->numeric(),
                TextEntry::make('admin_id')
                    ->numeric(),
                TextEntry::make('tanggal_pinjam')
                    ->date(),
                TextEntry::make('tenggat_pengembalian')
                    ->date(),
                TextEntry::make('tanggal_kembali')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
