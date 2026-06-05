<?php

namespace App\Filament\Resources\Wargas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WargaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nik'),
                TextEntry::make('nama'),
                TextEntry::make('alamat'),
                TextEntry::make('no_hp'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
