<?php

namespace Database\Factories;

use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use App\Models\StokBarang;
use App\Enums\KondisiBarang;
use App\Enums\StatusPeminjaman;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DetailPeminjaman>
 */
class DetailPeminjamanFactory extends Factory
{
    protected $model = DetailPeminjaman::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $peminjaman = Peminjaman::inRandomOrder()->first() ?? Peminjaman::factory()->create();
        $stokBarang = StokBarang::inRandomOrder()->first() ?? StokBarang::factory()->create();
        
        $jumlah = rand(1, 5);
        $kondisiKembali = null;
        $kembaliBaik = 0;
        $kembaliRusakRingan = 0;
        $kembaliRusakBerat = 0;

        if (in_array($peminjaman->status, [StatusPeminjaman::DIKEMBALIKAN, StatusPeminjaman::DIKEMBALIKAN_TERLAMBAT])) {
            $kondisiKembali = fake()->randomElement([
                KondisiBarang::BAIK,
                KondisiBarang::RUSAK_RINGAN,
                KondisiBarang::RUSAK_BERAT,
            ]);
            
            $kembali = $jumlah;
            $kembaliBaik = rand(0, $kembali);
            $kembali -= $kembaliBaik;
            if ($kembali > 0) {
                $kembaliRusakRingan = rand(0, $kembali);
                $kembali -= $kembaliRusakRingan;
                $kembaliRusakBerat = $kembali;
            }
        }

        return [
            "id_peminjaman" => $peminjaman->id,
            "id_stok_barang" => $stokBarang->id,
            "jumlah" => $jumlah,
            "kondisi_kembali" => $kondisiKembali,
            "jumlah_kembali_baik" => $kembaliBaik,
            "jumlah_kembali_rusak_ringan" => $kembaliRusakRingan,
            "jumlah_kembali_rusak_berat" => $kembaliRusakBerat,
        ];
    }
}
