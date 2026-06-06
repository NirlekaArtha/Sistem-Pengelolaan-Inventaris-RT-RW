<?php

namespace App\Filament\Resources\Dendas\Pages;

use App\Filament\Resources\Dendas\DendaResource;
use Filament\Resources\Pages\ListRecords;

class ListDendas extends ListRecords
{
    protected static string $resource = DendaResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
