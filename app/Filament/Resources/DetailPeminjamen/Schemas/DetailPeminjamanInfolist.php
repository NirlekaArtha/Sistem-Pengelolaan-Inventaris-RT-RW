<?php

namespace App\Filament\Resources\DetailPeminjamen\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Schemas\Schema;

class DetailPeminjamanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Peminjaman')
                    ->schema(fn (Schema $inner) => $inner->components([
                        TextEntry::make('peminjaman.id')
                            ->label('ID Peminjaman')
                            ->numeric(),
                        TextEntry::make('stokBarang.barang.nama_barang')
                            ->label('Nama Barang'),
                        TextEntry::make('stokBarang.kondisi')
                            ->label('Kondisi Pinjam')
                            ->badge()
                            ->color(fn ($state): string => match ($state instanceof \BackedEnum ? $state->value : $state) {
                                'baik' => 'success',
                                'rusak_ringan' => 'warning',
                                'rusak_berat' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('jumlah')
                            ->label('Jumlah Pinjam')
                            ->numeric(),
                        TextEntry::make('jumlah_kembali_baik')
                            ->label('Kembali Baik')
                            ->numeric(),
                        TextEntry::make('jumlah_kembali_rusak_ringan')
                            ->label('Kembali Rusak Ringan')
                            ->numeric(),
                        TextEntry::make('jumlah_kembali_rusak_berat')
                            ->label('Kembali Rusak Berat')
                            ->numeric(),
                        TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime()
                            ->placeholder('-'),
                    ]))
                    ->columns(4),
            ]);
    }
}
