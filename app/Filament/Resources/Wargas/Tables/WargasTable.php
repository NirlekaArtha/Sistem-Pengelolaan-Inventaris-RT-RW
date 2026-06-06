<?php

namespace App\Filament\Resources\Wargas\Tables;

use App\Models\Warga;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;

class WargasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('NIK')
                    ->label('NIK')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('alamat')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('no_hp')
                    ->label('No. HP')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('riwayat_peminjaman')
                    ->label('Riwayat')
                    ->icon('heroicon-o-clock')
                    ->color('info')
                    ->modalHeading(fn (Warga $record) => "Riwayat Peminjaman: {$record->nama}")
                    ->modalSubmitAction(false)
                    ->infolist(fn (Schema $schema) => $schema->components([
                        RepeatableEntry::make('peminjaman')
                            ->label('Daftar Peminjaman')
                            ->schema(fn (Schema $inner) => $inner->components([
                                TextEntry::make('tanggal_pinjam')
                                    ->label('Tanggal Pinjam')
                                    ->date(),
                                TextEntry::make('tenggat_pengembalian')
                                    ->label('Tenggat Kembali')
                                    ->date(),
                                TextEntry::make('tanggal_kembali')
                                    ->label('Tanggal Kembali')
                                    ->date()
                                    ->placeholder('Belum kembali'),
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge(),
                            ]))
                            ->columns(4)
                    ]))
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
