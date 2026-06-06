<?php

namespace App\Filament\Resources\Wargas\Pages;

use App\Filament\Resources\Wargas\WargaResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateWarga extends CreateRecord
{
    protected static string $resource = WargaResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Data Warga Ditambahkan')
            ->body('Warga baru berhasil didaftarkan ke sistem.');
    }
}
