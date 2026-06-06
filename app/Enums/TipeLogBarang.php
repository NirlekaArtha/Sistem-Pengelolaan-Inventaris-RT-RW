<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TipeLogBarang: string implements HasColor, HasLabel, HasIcon
{
    case MASUK = 'masuk';
    case KELUAR = 'keluar';

    public function getLabel(): string
    {
        return match ($this) {
            self::MASUK => 'Masuk',
            self::KELUAR => 'Keluar',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::MASUK => 'success',
            self::KELUAR => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::MASUK => 'heroicon-m-arrow-down-tray',
            self::KELUAR => 'heroicon-m-arrow-up-tray',
        };
    }
}
