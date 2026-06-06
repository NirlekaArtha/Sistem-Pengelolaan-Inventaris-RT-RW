<?php

namespace Database\Factories;

use App\Models\DetailPeminjaman;
use App\Models\StokBarang;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetailPeminjamanFactory extends Factory
{
    protected $model = DetailPeminjaman::class;

    public function definition(): array
    {
        // Cari stok barang yang tersedia
        $stokBarang = StokBarang::where("jumlah", ">", 0)->inRandomOrder()->first() 
            ?? StokBarang::factory()->create();
        
        return [
            "id_peminjaman" => null, // Akan diisi oleh Seeder / relasi parent
            "id_stok_barang" => $stokBarang->id,
            "jumlah" => rand(1, 5),
            "jumlah_kembali_baik" => 0,
            "jumlah_kembali_rusak_ringan" => 0,
            "jumlah_kembali_rusak_berat" => 0,
        ];
    }
}