<?php

namespace App\Filament\Resources\Dendas\Pages;

use App\Filament\Resources\Dendas\DendaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDenda extends EditRecord
{
    protected static string $resource = DendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
