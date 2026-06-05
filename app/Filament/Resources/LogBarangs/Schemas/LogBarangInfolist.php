<?php

namespace App\Filament\Resources\LogBarangs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LogBarangInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('barang_id')
                    ->numeric(),
                TextEntry::make('admin_id')
                    ->numeric(),
                TextEntry::make('tipe')
                    ->badge(),
                TextEntry::make('jumlah')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
