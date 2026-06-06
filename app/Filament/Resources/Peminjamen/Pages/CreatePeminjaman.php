<?php

namespace App\Filament\Resources\Peminjamen\Pages;

use App\Filament\Resources\Peminjamen\PeminjamanResource;
use App\Services\PeminjamanService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CreatePeminjaman extends CreateRecord
{
    protected static string $resource = PeminjamanResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $details = $data['detailPeminjaman'] ?? [];
        unset($data['detailPeminjaman']);

        /** @var PeminjamanService $peminjamanService */
        $peminjamanService = app(PeminjamanService::class);

        return $peminjamanService->buatPeminjaman($data, $details);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Peminjaman Dibuat')
            ->body('Data peminjaman baru berhasil dicatat.');
    }
}
