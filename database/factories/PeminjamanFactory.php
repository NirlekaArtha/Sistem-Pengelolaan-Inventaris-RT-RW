<?php

namespace Database\Factories;

use App\Models\Peminjaman;
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
        $statusList = ["dipinjam", "dikembalikan", "terlambat"];

        $tanggalPinjam = fake()->dateTimeBetween("-30 days", "now");
        $tenggat = (clone $tanggalPinjam)->modify("+7 days");

        $status = $statusList[array_rand($statusList)];
        $tanggalKembali =
            $status !== "dipinjam"
                ? fake()->dateTimeBetween($tanggalPinjam, "now")
                : null;

        return [
            "warga_id" => \App\Models\Warga::inRandomOrder()->first()->id ?? 1,
            "admin_id" => \App\Models\User::first()->id ?? 1,
            "tanggal_pinjam" => $tanggalPinjam,
            "tenggat_pengembalian" => $tenggat,
            "tanggal_kembali" => $tanggalKembali,
            "status" => $status,
        ];
    }
}
