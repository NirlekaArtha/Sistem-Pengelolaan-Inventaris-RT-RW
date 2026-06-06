<?php

namespace App\Enums;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum KondisiBarang: string implements HasColor, HasLabel
{
    case BAIK = 'baik';
    case RUSAK_RINGAN = 'rusak_ringan';
    case RUSAK_BERAT = 'rusak_berat';

    public function getLabel(): string
    {
        return match ($this) {
            self::BAIK => 'Baik',
            self::RUSAK_RINGAN => 'Rusak Ringan',
            self::RUSAK_BERAT => 'Rusak Berat',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::BAIK => 'success',
            self::RUSAK_RINGAN => 'warning',
            self::RUSAK_BERAT => 'danger',
        };
    }
}
