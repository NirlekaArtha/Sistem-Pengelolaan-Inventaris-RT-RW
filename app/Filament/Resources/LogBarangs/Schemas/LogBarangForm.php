<?php

namespace App\Filament\Resources\LogBarangs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LogBarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('barang_id')
                    ->required()
                    ->numeric(),
                TextInput::make('admin_id')
                    ->required()
                    ->numeric(),
                Select::make('tipe')
                    ->options(['masuk' => 'Masuk', 'keluar' => 'Keluar'])
                    ->required(),
                TextInput::make('jumlah')
                    ->required()
                    ->numeric(),
            ]);
    }
}
