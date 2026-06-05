<?php

namespace Database\Factories;

use App\Models\Warga;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Warga>
 */
class WargaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "NIK" => fake()->unique()->numerify("################"),
            "nama" => fake()->name(),
            "alamat" => "Jl. " . fake()->streetName() . " No. " . rand(1, 100),
            "no_hp" => "08" . fake()->numerify("##########"),
        ];
    }
}
