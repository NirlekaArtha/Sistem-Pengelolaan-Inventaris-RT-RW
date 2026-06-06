<?php

namespace App\Filament\Resources\Wargas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

class WargaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // === Data Identitas ===
            Section::make('Data Identitas')
                ->description('Informasi identitas warga berdasarkan KTP')
                ->icon(Heroicon::OutlinedIdentification)
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('nama')
                            ->label('Nama Lengkap')
                            ->icon(Heroicon::OutlinedUser)
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large)
                            ->columnSpan(2),

                        TextEntry::make('NIK')
                            ->label('NIK')
                            ->icon(Heroicon::OutlinedIdentification)
                            ->copyable()
                            ->copyMessage('NIK disalin!')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('created_at')
                            ->label('Terdaftar Sejak')
                            ->icon(Heroicon::OutlinedCalendar)
                            ->dateTime('d M Y, H:i')
                            ->placeholder('—'),
                    ]),
                ]),

            // === Kontak & Alamat ===
            Section::make('Kontak & Alamat')
                ->description('Informasi tempat tinggal dan kontak warga')
                ->icon(Heroicon::OutlinedMapPin)
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('alamat')
                            ->label('Alamat Lengkap')
                            ->icon(Heroicon::OutlinedHome)
                            ->placeholder('—')
                            ->columnSpan(2),

                        TextEntry::make('no_hp')
                            ->label('Nomor HP')
                            ->icon(Heroicon::OutlinedPhone)
                            ->copyable()
                            ->copyMessage('Nomor HP disalin!')
                            ->placeholder('—'),

                        TextEntry::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->icon(Heroicon::OutlinedPencilSquare)
                            ->dateTime('d M Y, H:i')
                            ->placeholder('—'),
                    ]),
                ]),
        ]);
    }
}
