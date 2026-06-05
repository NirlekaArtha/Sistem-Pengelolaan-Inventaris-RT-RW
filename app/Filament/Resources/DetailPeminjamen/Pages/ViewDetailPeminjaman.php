<?php

namespace App\Filament\Resources\DetailPeminjamen\Pages;

use App\Filament\Resources\DetailPeminjamen\DetailPeminjamanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDetailPeminjaman extends ViewRecord
{
    protected static string $resource = DetailPeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
