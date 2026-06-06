<?php

namespace App\Filament\Resources\LogBarangs\Schemas;

use App\Enums\KondisiBarang;
use App\Enums\TipeLogBarang;
use App\Models\Barang;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class LogBarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // === Identifikasi Barang ===
            Section::make('Identifikasi Barang')
                ->description('Pilih barang dan kondisi yang akan dicatat dalam log')
                ->icon(Heroicon::OutlinedCube)
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('id_barang')
                            ->label('Nama Barang')
                            ->relationship('barang', 'nama_barang')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Pilih barang...')
                            ->prefixIcon(Heroicon::OutlinedArchiveBox),

                        Select::make('kondisi')
                            ->label('Kondisi Barang')
                            ->options(KondisiBarang::class)
                            ->required()
                            ->prefixIcon(Heroicon::OutlinedTag),
                    ]),
                ]),

            // === Detail Log ===
            Section::make('Detail Log')
                ->description('Isi tipe pergerakan dan jumlah barang')
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('tipe')
                            ->label('Tipe Log')
                            ->options(TipeLogBarang::class)
                            ->required()
                            ->prefixIcon(Heroicon::OutlinedArrowsRightLeft),

                        TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->suffix('unit')
                            ->prefixIcon(Heroicon::OutlinedHashtag),
                    ]),

                    Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->placeholder('Catatan tambahan mengenai log ini...')
                        ->rows(3)
                        ->nullable()
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
