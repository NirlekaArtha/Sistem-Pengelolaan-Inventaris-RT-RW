<?php

namespace App\Filament\Resources\DetailPeminjamen\Pages;

use App\Filament\Resources\DetailPeminjamen\DetailPeminjamanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDetailPeminjamen extends ListRecords
{
    protected static string $resource = DetailPeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
