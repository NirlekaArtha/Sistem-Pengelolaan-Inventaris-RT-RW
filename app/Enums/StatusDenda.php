<?php

namespace App\Enums;

enum StatusDenda: string
{
    case DIBAYAR = 'dibayar';
    case BELUM_DIBAYAR = 'belum_dibayar';
}
