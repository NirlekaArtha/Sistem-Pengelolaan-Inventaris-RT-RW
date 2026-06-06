<?php

namespace App\Filament\Resources\LogBarangs\Pages;

use App\Filament\Resources\LogBarangs\LogBarangResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateLogBarang extends CreateRecord
{
    protected static string $resource = LogBarangResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Log Barang Dicatat')
            ->body('Entri log barang baru berhasil disimpan.');
    }
}
