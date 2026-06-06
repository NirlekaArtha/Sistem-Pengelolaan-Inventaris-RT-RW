<?php

namespace App\Filament\Resources\Dendas\Schemas;

use App\Enums\StatusDenda;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DendaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_peminjaman')
                    ->required()
                    ->numeric(),
                TextInput::make('jumlah')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options(StatusDenda::class)
                    ->default('belum_dibayar')
                    ->required(),
            ]);
    }
}
