<?php

namespace App\Filament\Resources\Dendas\Tables;

use App\Models\Denda;
use App\Enums\StatusDenda;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class DendasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('peminjaman')
                    ->label('Peminjaman')
                    ->getStateUsing(function (Denda $record) {
                        $namaWarga = $record->peminjaman->warga->nama ?? '-';
                        $tanggalPinjam = $record->peminjaman->tanggal_pinjam 
                            ? $record->peminjaman->tanggal_pinjam->format('d/m/Y') 
                            : '-';
                        return "{$namaWarga} ({$tanggalPinjam})";
                    })
                    ->searchable(query: function ($query, string $search) {
                        $query->whereHas('peminjaman.warga', function ($q) use ($search) {
                            $q->where('nama', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('jumlah')
                    ->label('Jumlah Denda')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof \BackedEnum ? $state->value : $state) {
                        'dibayar' => 'success',
                        'belum_dibayar' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('tandai_lunas')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Denda $record) => ($record->status->value ?? $record->status) === 'belum_dibayar')
                    ->requiresConfirmation()
                    ->action(function (Denda $record) {
                        $record->update(['status' => StatusDenda::DIBAYAR]);
                        Notification::make()
                            ->title('Denda Ditandai Lunas')
                            ->success()
                            ->send();
                    })
            ])
            ->toolbarActions([]);
    }
}
