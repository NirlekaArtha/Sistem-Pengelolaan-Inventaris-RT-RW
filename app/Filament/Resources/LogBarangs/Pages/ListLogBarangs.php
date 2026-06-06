<?php

namespace App\Filament\Resources\LogBarangs\Pages;

use App\Filament\Resources\LogBarangs\LogBarangResource;
use Filament\Resources\Pages\ListRecords;

class ListLogBarangs extends ListRecords
{
    protected static string $resource = LogBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
