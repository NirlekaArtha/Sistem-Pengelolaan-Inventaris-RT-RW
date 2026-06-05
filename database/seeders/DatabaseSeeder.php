<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Warga;
use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\LogBarang;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // admin
        User::factory()->create([
            "name" => "Admin",
            "email" => "admin@gmail.com",
            "password" => Hash::make("admin123"),
        ]);

        // data utama
        Warga::factory(10)->create();
        Barang::factory(12)->create();

        // peminjaman
        Peminjaman::factory(20)->create();

        // detail
        DetailPeminjaman::factory(26)->create();

        // log
        LogBarang::factory(30)->create();
    }
}
