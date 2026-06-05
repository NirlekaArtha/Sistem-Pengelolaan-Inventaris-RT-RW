<?php

namespace Database\Factories;

use App\Models\StokBarang;
use App\Models\Barang;
use App\Enums\KondisiBarang;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StokBarang>
 */
class StokBarangFactory extends Factory
{
    protected $model = StokBarang::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "id_barang" => Barang::inRandomOrder()->first()?->id ?? Barang::factory(),
            "kondisi" => fake()->randomElement([
                KondisiBarang::BAIK,
                KondisiBarang::RUSAK_RINGAN,
                KondisiBarang::RUSAK_BERAT,
            ]),
            "jumlah" => rand(5, 50),
        ];
    }
}
