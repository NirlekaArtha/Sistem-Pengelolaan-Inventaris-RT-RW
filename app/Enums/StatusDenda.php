<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusDenda: string implements HasColor, HasLabel
{
    case DIBAYAR = 'dibayar';
    case BELUM_DIBAYAR = 'belum_dibayar';

    public function getLabel(): string
    {
        return match ($this) {
            self::DIBAYAR => 'Dibayar',
            self::BELUM_DIBAYAR => 'Belum Dibayar',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DIBAYAR => 'success',
            self::BELUM_DIBAYAR => 'danger',
        };
    }
}
