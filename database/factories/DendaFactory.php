<?php

namespace Database\Factories;

use App\Models\Denda;
use App\Models\Peminjaman;
use App\Enums\StatusDenda;
use App\Enums\StatusPeminjaman;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Denda>
 */
class DendaFactory extends Factory
{
    protected $model = Denda::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $peminjaman = Peminjaman::whereIn('status', [
            StatusPeminjaman::TERLAMBAT,
            StatusPeminjaman::DIKEMBALIKAN_TERLAMBAT,
        ])->inRandomOrder()->first() ?? Peminjaman::factory()->create([
            'status' => StatusPeminjaman::TERLAMBAT,
        ]);

        return [
            "id_peminjaman" => $peminjaman->id,
            "jumlah" => rand(5, 50) * 1000, // denda kelipatan 1000
            "status" => fake()->randomElement([
                StatusDenda::DIBAYAR,
                StatusDenda::BELUM_DIBAYAR,
            ]),
        ];
    }
}
