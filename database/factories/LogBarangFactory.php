<?php

namespace Database\Factories;

use App\Models\LogBarang;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LogBarang>
 */
class LogBarangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "barang_id" =>
                \App\Models\Barang::inRandomOrder()->first()->id ?? 1,
            "admin_id" => \App\Models\User::first()->id ?? 1,
            "tipe" => fake()->randomElement(["masuk", "keluar"]),
            "jumlah" => rand(1, 5),
        ];
    }
}
