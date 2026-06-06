<?php

namespace App\Enums;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TipeLogBarang: string implements HasColor, HasLabel
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
}
