<?php

namespace App\Filament\Resources\DetailPeminjamen\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DetailPeminjamanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('peminjaman_id')
                    ->required()
                    ->numeric(),
                TextInput::make('barang_id')
                    ->required()
                    ->numeric(),
                TextInput::make('jumlah')
                    ->required()
                    ->numeric(),
                Select::make('kondisi_saat_pinjam')
                    ->options(['baik' => 'Baik', 'rusak ringan' => 'Rusak ringan', 'rusak berat' => 'Rusak berat'])
                    ->required(),
                Select::make('kondisi_saat_kembali')
                    ->options(['baik' => 'Baik', 'rusak ringan' => 'Rusak ringan', 'rusak berat' => 'Rusak berat'])
                    ->default(null),
            ]);
    }
}
