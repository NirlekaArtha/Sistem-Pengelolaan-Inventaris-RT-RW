<?php

namespace App\Filament\Resources\LogBarangs\Pages;

use App\Filament\Resources\LogBarangs\LogBarangResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLogBarang extends EditRecord
{
    protected static string $resource = LogBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
