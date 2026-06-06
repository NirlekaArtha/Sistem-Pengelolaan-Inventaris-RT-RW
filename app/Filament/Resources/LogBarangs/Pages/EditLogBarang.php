<?php

namespace App\Filament\Resources\LogBarangs\Pages;

use App\Filament\Resources\LogBarangs\LogBarangResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditLogBarang extends EditRecord
{
    protected static string $resource = LogBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->icon('heroicon-o-eye'),
            DeleteAction::make()
                ->icon('heroicon-o-trash'),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Log Barang Diperbarui')
            ->body('Entri log barang berhasil diperbarui.');
    }
}
