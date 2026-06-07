<?php

namespace App\Filament\Widgets;

use App\Models\Peminjaman;
use App\Enums\StatusPeminjaman;
use Filament\Widgets\ChartWidget;

class StatusPeminjamanChart extends ChartWidget
{
    protected static ?int $sort = 5;

    protected ?string $heading = 'Proporsi Status Peminjaman';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $dikembalikan = Peminjaman::where('status', StatusPeminjaman::DIKEMBALIKAN)->count();
        $terlambat = Peminjaman::where('status', StatusPeminjaman::TERLAMBAT)->count();
        $dipinjam = Peminjaman::where('status', StatusPeminjaman::DIPINJAM)->count();
        $dikembalikanTerlambat = Peminjaman::where('status', StatusPeminjaman::DIKEMBALIKAN_TERLAMBAT)->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Transaksi',
                    'data' => [$dikembalikan, $terlambat, $dipinjam, $dikembalikanTerlambat],
                    'backgroundColor' => [
                        '#10B981', // Dikembalikan (Success Green)
                        '#EF4444', // Terlambat (Danger Red)
                        '#F59E0B', // Dipinjam (Warning Orange)
                        '#8B5CF6', // Dikembalikan Terlambat (Purple)
                    ],
                    "borderWidth" => 0,
                    "hoverOffset" => 5,
                ],
            ],
            'labels' => ['Dikembalikan', 'Terlambat', 'Dipinjam', 'Dikembalikan Terlambat'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
