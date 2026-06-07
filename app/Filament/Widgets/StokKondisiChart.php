<?php

namespace App\Filament\Widgets;

use App\Models\StokBarang;
use App\Enums\KondisiBarang;
use Filament\Widgets\ChartWidget;

class StokKondisiChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Status Kondisi Stok Barang';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $baik = (int) StokBarang::where('kondisi', KondisiBarang::BAIK)->sum('jumlah_total');
        $rusakRingan = (int) StokBarang::where('kondisi', KondisiBarang::RUSAK_RINGAN)->sum('jumlah_total');
        $rusakBerat = (int) StokBarang::where('kondisi', KondisiBarang::RUSAK_BERAT)->sum('jumlah_total');

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Stok',
                    'data' => [$baik, $rusakRingan, $rusakBerat],
                    'backgroundColor' => [
                        '#10B981', // Baik (Success)
                        '#F59E0B', // Rusak Ringan (Warning)
                        '#EF4444', // Rusak Berat (Danger)
                    ],
                    "borderWidth" => 0,
                    "hoverOffset" => 5,
                ],
            ],
            'labels' => ['Baik', 'Rusak Ringan', 'Rusak Berat'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
