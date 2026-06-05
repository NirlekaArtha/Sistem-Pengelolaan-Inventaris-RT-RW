<?php

namespace App\Enums;

enum StatusPeminjaman: string
{
    case DIPINJAM = 'dipinjam';
    case DIKEMBALIKAN = 'dikembalikan';
    case TERLAMBAT = 'terlambat';
    case DIKEMBALIKAN_TERLAMBAT = 'dikembalikan_terlambat';
}
