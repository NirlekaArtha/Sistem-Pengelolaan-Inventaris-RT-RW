<?php

namespace App\Filament\Widgets;

use App\Models\Peminjaman;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PeminjamanBulananChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Total Peminjaman Bulanan (12 Bulan Terakhir)';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $now = Carbon::now();
        $limitDate = now()->subMonths(11)->startOfMonth();

        // Cari tanggal peminjaman paling awal dalam 12 bulan terakhir
        $earliestDateString = Peminjaman::where('tanggal_pinjam', '>=', $limitDate)->min('tanggal_pinjam');

        if (!$earliestDateString) {
            return [
                'datasets' => [
                    [
                        'label' => 'Total Peminjaman',
                        'data' => [],
                    ],
                ],
                'labels' => [],
            ];
        }

        $earliestDate = Carbon::parse($earliestDateString)->startOfMonth();

        // Buat daftar bulan dari tanggal paling awal sampai sekarang
        $months = [];
        $current = $earliestDate->copy();
        while ($current->lte($now)) {
            $months[$current->format('Y-m')] = [
                'label' => $current->translatedFormat('F Y'),
                'count' => 0,
            ];
            $current->addMonth();
        }

        // Ambil jumlah peminjaman dikelompokkan berdasarkan bulan
        $loans = Peminjaman::selectRaw("DATE_FORMAT(tanggal_pinjam, '%Y-%m') as month, COUNT(*) as count")
            ->where('tanggal_pinjam', '>=', $earliestDate)
            ->groupBy('month')
            ->get();

        foreach ($loans as $loan) {
            if (isset($months[$loan->month])) {
                $months[$loan->month]['count'] = (int) $loan->count;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Peminjaman',
                    'data' => array_column($months, 'count'),
                    'borderColor' => '#2563EB',
                    'backgroundColor' => 'rgba(37, 99, 235, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => array_column($months, 'label'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
