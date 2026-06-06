<?php

namespace App\Filament\Resources\DetailPeminjamen\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;

class DetailPeminjamanForm
{
    // DetailPeminjaman is managed internally through PeminjamanResource.
    // This form is kept minimal since the resource is navigation-hidden.
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([]);
    }
}
