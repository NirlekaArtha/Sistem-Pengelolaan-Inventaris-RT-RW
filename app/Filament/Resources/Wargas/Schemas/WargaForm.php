<?php

namespace App\Filament\Resources\Wargas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class WargaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // === Data Identitas ===
            Section::make('Data Identitas')
                ->description('Isi informasi identitas warga sesuai KTP')
                ->icon(Heroicon::OutlinedIdentification)
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('NIK')
                            ->label('NIK')
                            ->required()
                            ->numeric()
                            ->length(16)
                            ->unique(table: 'warga', column: 'NIK', ignoreRecord: true)
                            ->placeholder('16 digit NIK sesuai KTP')
                            ->prefixIcon(Heroicon::OutlinedIdentification)
                            ->helperText('Nomor Induk Kependudukan 16 digit'),

                        TextInput::make('nama')
                            ->label('Nama Lengkap')
                            ->required()
                            ->placeholder('Nama sesuai KTP')
                            ->prefixIcon(Heroicon::OutlinedUser)
                            ->maxLength(255),
                    ]),
                ]),

            // === Kontak & Alamat ===
            Section::make('Kontak & Alamat')
                ->description('Informasi tempat tinggal dan nomor yang dapat dihubungi')
                ->icon(Heroicon::OutlinedMapPin)
                ->schema([
                    TextInput::make('alamat')
                        ->label('Alamat Lengkap')
                        ->required()
                        ->placeholder('Jl. Contoh No. 1, RT/RW...')
                        ->prefixIcon(Heroicon::OutlinedHome)
                        ->maxLength(500)
                        ->columnSpanFull(),

                    TextInput::make('no_hp')
                        ->label('Nomor HP / WhatsApp')
                        ->required()
                        ->numeric()
                        ->placeholder('Contoh: 08123456789')
                        ->prefixIcon(Heroicon::OutlinedPhone)
                        ->helperText('Nomor yang aktif dan dapat dihubungi'),
                ]),
        ]);
    }
}
