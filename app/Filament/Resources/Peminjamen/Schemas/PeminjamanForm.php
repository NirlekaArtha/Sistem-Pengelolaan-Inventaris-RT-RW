<?php

namespace App\Filament\Resources\Peminjamen\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PeminjamanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('warga_id')
                    ->required()
                    ->numeric(),
                TextInput::make('admin_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('tanggal_pinjam')
                    ->required(),
                DatePicker::make('tenggat_pengembalian')
                    ->required(),
                DatePicker::make('tanggal_kembali'),
                Select::make('status')
                    ->options(['dipinjam' => 'Dipinjam', 'dikembalikan' => 'Dikembalikan', 'terlambat' => 'Terlambat'])
                    ->required(),
            ]);
    }
}
