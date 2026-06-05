<?php

namespace Database\Factories;

use App\Models\DetailPeminjaman;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DetailPeminjaman>
 */
class DetailPeminjamanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kondisi = ["baik", "rusak ringan", "rusak berat"];

        return [
            "peminjaman_id" =>
                \App\Models\Peminjaman::inRandomOrder()->first()->id ?? 1,
            "barang_id" =>
                \App\Models\Barang::inRandomOrder()->first()->id ?? 1,
            "jumlah" => rand(1, 3),
            "kondisi_saat_pinjam" => $kondisi[array_rand($kondisi)],
            "kondisi_saat_kembali" => rand(0, 1)
                ? $kondisi[array_rand($kondisi)]
                : null,
        ];
    }
}
