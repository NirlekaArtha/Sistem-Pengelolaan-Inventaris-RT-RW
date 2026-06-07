<?php

namespace App\Filament\Widgets;

use App\Models\DetailPeminjaman;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopBarangChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Top 5 Barang Paling Sering Dipinjam';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $topBarang = DetailPeminjaman::join('stok_barang', 'detail_peminjaman.id_stok_barang', '=', 'stok_barang.id')
            ->join('barang', 'stok_barang.id_barang', '=', 'barang.id')
            ->select('barang.nama_barang', DB::raw('SUM(detail_peminjaman.jumlah) as total_dipinjam'))
            ->groupBy('barang.id', 'barang.nama_barang')
            ->orderByDesc('total_dipinjam')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Unit Dipinjam',
                    'data' => $topBarang->pluck('total_dipinjam')->toArray(),
                    'backgroundColor' => [
                        '#3B82F6', // Blue
                        '#10B981', // Green
                        '#F59E0B', // Yellow
                        '#EF4444', // Red
                        '#8B5CF6', // Purple
                    ],
                    "borderWidth" => 0,
                ],
            ],
            'labels' => $topBarang->pluck('nama_barang')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
