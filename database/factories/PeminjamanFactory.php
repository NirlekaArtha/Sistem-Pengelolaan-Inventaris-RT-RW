<?php

namespace Database\Factories;

use App\Models\Peminjaman;
use App\Models\Warga;
use App\Models\User;
use App\Enums\StatusPeminjaman;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Peminjaman>
 */
class PeminjamanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statusList = [
            StatusPeminjaman::DIPINJAM,
            StatusPeminjaman::DIKEMBALIKAN,
            StatusPeminjaman::TERLAMBAT,
            StatusPeminjaman::DIKEMBALIKAN_TERLAMBAT,
        ];

        // 1. Shift the initial booking window slightly back so "tenggat" doesn't overshoot "now" as often,
        // OR ensure your conditional limits adapt dynamically.
        $tanggalPinjam = fake()->dateTimeBetween("-30 days", "now");
        $tenggat = (clone $tanggalPinjam)->modify("+7 days");

        $status = $statusList[array_rand($statusList)];
        
        $tanggalKembali = null;
        if ($status === StatusPeminjaman::DIKEMBALIKAN) {
            $tanggalKembali = fake()->dateTimeBetween($tanggalPinjam, $tenggat);
        } elseif ($status === StatusPeminjaman::DIKEMBALIKAN_TERLAMBAT) {
            // FIX: Ensure the end date is always ahead of the deadline (e.g., 1 to 14 days after $tenggat)
            $maxReturnDate = (clone $tenggat)->modify("+" . rand(1, 14) . " days");
            $tanggalKembali = fake()->dateTimeBetween($tenggat, $maxReturnDate);
        } 

        return [
            "id_warga" => Warga::inRandomOrder()->first()?->id ?? Warga::factory(),
            "id_admin" => User::inRandomOrder()->first()?->id ?? User::factory(),
            "tanggal_pinjam" => $tanggalPinjam,
            "tenggat_pengembalian" => $tenggat,
            "tanggal_kembali" => $tanggalKembali,
            "status" => $status,
        ];
    }
}
