<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusPeminjaman: string implements HasColor, HasLabel
{
    case DIPINJAM = 'dipinjam';
    case DIKEMBALIKAN = 'dikembalikan';
    case TERLAMBAT = 'terlambat';
    case DIKEMBALIKAN_TERLAMBAT = 'dikembalikan_terlambat';

    public function getLabel(): string
    {
        return match ($this) {
            self::DIPINJAM => 'Dipinjam',
            self::DIKEMBALIKAN => 'Dikembalikan',
            self::TERLAMBAT => 'Terlambat',
            self::DIKEMBALIKAN_TERLAMBAT => 'Dikembalikan Terlambat',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DIPINJAM => 'warning',
            self::DIKEMBALIKAN => 'success',
            self::TERLAMBAT => 'danger',
            self::DIKEMBALIKAN_TERLAMBAT => 'danger',
        };
    }
}
