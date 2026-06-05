<?php

namespace App\Filament\Resources\Wargas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WargaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nik')
                    ->required(),
                TextInput::make('nama')
                    ->required(),
                TextInput::make('alamat')
                    ->required(),
                TextInput::make('no_hp')
                    ->required(),
            ]);
    }
}
