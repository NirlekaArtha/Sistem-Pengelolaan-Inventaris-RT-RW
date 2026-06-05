<?php

namespace App\Filament\Resources\Barangs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make("nama_barang")->required(),
            TextInput::make("jumlah_total")->required()->numeric(),
            TextInput::make("jumlah_tersedia")->required()->numeric(),
            Select::make("kondisi")
                ->options([
                    "baik" => "Baik",
                    "rusak ringan" => "Rusak ringan",
                    "rusak berat" => "Rusak berat",
                ])
                ->required(),
            TextInput::make("lokasi_penyimpanan")->required(),
        ]);
    }
}
