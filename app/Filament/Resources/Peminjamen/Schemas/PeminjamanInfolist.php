<?php

namespace App\Filament\Resources\Peminjamen\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

class PeminjamanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // === Informasi Peminjaman ===
            Section::make('Informasi Peminjaman')
                ->description('Detail data peminjaman barang inventaris RT/RW')
                ->columnSpanFull()
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('warga.nama')
                            ->label('Nama Warga')
                            ->icon(Heroicon::OutlinedUser)
                            ->weight(FontWeight::SemiBold)
                            ->size(TextSize::Large),

                        TextEntry::make('admin.name')
                            ->label('Diproses Oleh')
                            ->icon(Heroicon::OutlinedUserCircle)
                            ->placeholder('—'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->icon(Heroicon::OutlinedTag)
                            ->badge()
                            ->color(
                                fn ($state): string => match (
                                    $state instanceof \BackedEnum
                                        ? $state->value
                                        : $state
                                ) {
                                    'dikembalikan'         => 'success',
                                    'dipinjam'             => 'warning',
                                    'terlambat'            => 'danger',
                                    'dikembalikan_terlambat' => 'info',
                                    default                => 'gray',
                                },
                            ),

                        TextEntry::make('tanggal_pinjam')
                            ->label('Tanggal Pinjam')
                            ->icon(Heroicon::OutlinedCalendar)
                            ->date('d M Y'),

                        TextEntry::make('tenggat_pengembalian')
                            ->label('Tenggat Pengembalian')
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->date('d M Y'),

                        TextEntry::make('tanggal_kembali')
                            ->label('Tanggal Dikembalikan')
                            ->icon(Heroicon::OutlinedCheckCircle)
                            ->date('d M Y')
                            ->placeholder('Belum dikembalikan'),
                    ]),
                ]),

            // === Daftar Barang yang Dipinjam ===
            Section::make('Barang yang Dipinjam')
                ->description('Rincian item barang yang dipinjam beserta status pengembaliannya')
                ->icon(Heroicon::OutlinedArchiveBox)
                ->columnSpanFull()
                ->schema([
                    RepeatableEntry::make('detailPeminjaman')
                        ->label('')
                        ->schema([
                            Grid::make(7)->schema([
                                TextEntry::make('stokBarang.barang.nama_barang')
                                    ->label('Nama Barang')
                                    ->icon(Heroicon::OutlinedCube)
                                    ->weight(FontWeight::SemiBold)
                                    ->columnSpan(2),

                                TextEntry::make('stokBarang.kondisi')
                                    ->label('Kondisi Pinjam')
                                    ->badge()
                                    ->color(
                                        fn ($state): string => match (
                                            $state instanceof \BackedEnum
                                                ? $state->value
                                                : $state
                                        ) {
                                            'baik'         => 'success',
                                            'rusak_ringan' => 'warning',
                                            'rusak_berat'  => 'danger',
                                            default        => 'gray',
                                        },
                                    ),

                                TextEntry::make('jumlah')
                                    ->label('Jumlah Pinjam')
                                    ->numeric()
                                    ->suffix(' unit')
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('jumlah_kembali_baik')
                                    ->label('🟢 Kembali Baik')
                                    ->numeric()
                                    ->suffix(' unit')
                                    ->placeholder('0'),

                                TextEntry::make('jumlah_kembali_rusak_ringan')
                                    ->label('🟠 Rusak Ringan')
                                    ->numeric()
                                    ->suffix(' unit')
                                    ->placeholder('0'),
                                
                                TextEntry::make('jumlah_kembali_rusak_berat')
                                    ->label('🔴 Rusak Berat')
                                    ->numeric()
                                    ->suffix(' unit')
                                    ->placeholder('0'),
                            ]),
                        ]),
                ]),

            // === Informasi Denda ===
            Section::make('Informasi Denda')
                ->description('Data denda keterlambatan pengembalian barang')
                ->icon(Heroicon::OutlinedExclamationCircle)
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('denda.jumlah')
                            ->label('Jumlah Denda')
                            ->icon(Heroicon::OutlinedBanknotes)
                            ->money('IDR')
                            ->placeholder('Rp 0')
                            ->color('danger')
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),

                        TextEntry::make('denda.status')
                            ->label('Status Denda')
                            ->icon(Heroicon::OutlinedCheckBadge)
                            ->badge()
                            ->color(
                                fn ($state): string => match (
                                    $state instanceof \BackedEnum
                                        ? $state->value
                                        : $state
                                ) {
                                    'dibayar'      => 'success',
                                    'belum_dibayar' => 'danger',
                                    default        => 'gray',
                                },
                            )
                            ->placeholder('—'),
                    ]),
                ])
                ->visible(fn ($record) => $record && $record->denda()->exists()),
        ]);
    }
}
