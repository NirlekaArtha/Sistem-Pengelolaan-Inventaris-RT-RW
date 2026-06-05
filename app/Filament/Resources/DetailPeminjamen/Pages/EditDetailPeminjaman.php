<?php

namespace App\Filament\Resources\DetailPeminjamen\Pages;

use App\Filament\Resources\DetailPeminjamen\DetailPeminjamanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDetailPeminjaman extends EditRecord
{
    protected static string $resource = DetailPeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
