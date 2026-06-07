<?php

namespace App\Filament\Widgets;

use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Denda;
use App\Enums\StatusPeminjaman;
use App\Enums\StatusDenda;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalPinjamanAktif = Peminjaman::whereIn("status", [
            StatusPeminjaman::DIPINJAM,
            StatusPeminjaman::TERLAMBAT,
        ])->count();

        $jumlahBarangDipinjam = DetailPeminjaman::whereHas(
            "peminjaman",
            function ($query) {
                $query->whereIn("status", [
                    StatusPeminjaman::DIPINJAM,
                    StatusPeminjaman::TERLAMBAT,
                ]);
            },
        )->sum("jumlah");

        $peminjamanTelat = Peminjaman::where(
            "status",
            StatusPeminjaman::TERLAMBAT,
        )->count();

        $totalDendaBelumDibayar = Denda::where(
            "status",
            StatusDenda::BELUM_DIBAYAR,
        )->sum("jumlah");

        return [
            Stat::make(
                "Total Pinjaman Aktif",
                $totalPinjamanAktif . " Pinjaman",
            )
                ->description("Belum dikembalikan")
                ->descriptionIcon("heroicon-m-arrow-path")
                ->color("primary")
                ->icon("heroicon-m-clipboard-document-list"),

            Stat::make(
                "Stok Barang Sedang Dipinjam",
                $jumlahBarangDipinjam . " unit",
            )
                ->description("Sedang berada di warga")
                ->descriptionIcon("heroicon-m-cube")
                ->color("warning")
                ->icon("heroicon-m-cube-transparent"),

            Stat::make("Peminjaman Terlambat", $peminjamanTelat . " Pinjaman")
                ->description("Melewati batas tenggat waktu")
                ->descriptionIcon("heroicon-m-clock")
                ->color("danger")
                ->icon("heroicon-m-exclamation-triangle"),

            Stat::make(
                "Total Denda Belum Dibayar",
                "Rp " . number_format($totalDendaBelumDibayar, 0, ",", "."),
            )
                ->description("Denda keterlambatan warga")
                ->descriptionIcon("heroicon-m-banknotes")
                ->color("danger")
                ->icon("heroicon-m-credit-card"),
        ];
    }
}
