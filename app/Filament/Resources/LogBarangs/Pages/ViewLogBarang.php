<?php

namespace App\Filament\Resources\LogBarangs\Pages;

use App\Filament\Resources\LogBarangs\LogBarangResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLogBarang extends ViewRecord
{
    protected static string $resource = LogBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->icon('heroicon-o-pencil-square'),
        ];
    }
}
