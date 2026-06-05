<?php

namespace Database\Factories;

use App\Models\Barang;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Barang>
 */
class BarangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $total = rand(5, 20);
        $kondisi = ["baik", "rusak ringan", "rusak berat"];
        $namaBarang = [
            "Laptop",
            "Proyektor",
            "Printer",
            "Kamera",
            "Speaker",
            "Mikrofon",
            "Kabel HDMI",
            "Mouse",
            "Keyboard",
            "Monitor",
            "Router",
            "Switch",
            "Flashdisk",
            "Harddisk",
            "Scanner",
            "Tripod",
            "Whiteboard",
            "Kursi Lipat",
            "Meja Lipat",
            "Extension Kabel",
        ];

        return [
            "nama_barang" => fake()->unique()->randomElement($namaBarang),
            "jumlah_total" => $total,
            "jumlah_tersedia" => $total,
            "kondisi" => $kondisi[array_rand($kondisi)],
            "lokasi_penyimpanan" => "gudang",
        ];
    }
}
