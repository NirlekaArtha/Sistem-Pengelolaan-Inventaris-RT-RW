<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Warga;
use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\LogBarang;
use App\Models\StokBarang;
use App\Models\Denda;
use App\Enums\KondisiBarang;
use App\Enums\StatusPeminjaman;
use App\Enums\StatusDenda;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // admin
        User::factory()->create([
            "nama"     => "Admin",
            "email"    => "admin@gmail.com",
            "password" => Hash::make("admin123"),
        ]);

        // data utama
        Warga::factory(10)->create();
        Barang::factory(12)->create();

        // Buat stok barang secara langsung untuk setiap barang,
        // satu baris per kondisi (baik, rusak ringan, rusak berat).
        Barang::all()->each(function (Barang $barang) {
            $stokPerKondisi = [
                ['kondisi' => KondisiBarang::BAIK,         'jumlah' => rand(5, 20)],
                ['kondisi' => KondisiBarang::RUSAK_RINGAN, 'jumlah' => rand(1, 5)],
                ['kondisi' => KondisiBarang::RUSAK_BERAT,  'jumlah' => rand(0, 3)],
            ];

            $totalStok = 0;
            foreach ($stokPerKondisi as $stok) {
                StokBarang::create([
                    'id_barang' => $barang->id,
                    'kondisi'   => $stok['kondisi'],
                    'jumlah'    => $stok['jumlah'],
                ]);
                $totalStok += $stok['jumlah'];
            }

            // Sinkronkan jumlah_total barang dengan total seluruh stok
            $barang->update(['jumlah_total' => $totalStok]);
        });

        // peminjaman
        Peminjaman::factory(20)->create();

        // detail
        DetailPeminjaman::factory(26)->create();

        // log
        LogBarang::factory(30)->create();

        // Buat denda otomatis untuk setiap peminjaman berstatus "dikembalikan_terlambat".
        // Nominal: Rp 5.000 per hari keterlambatan (tanggal_kembali - tenggat_pengembalian).
        Peminjaman::where('status', StatusPeminjaman::DIKEMBALIKAN_TERLAMBAT)
            ->doesntHave('denda')
            ->each(function (Peminjaman $peminjaman) {
                $selisihHari = (int) $peminjaman->tenggat_pengembalian
                    ->diffInDays($peminjaman->tanggal_kembali);

                // Minimal 1 hari agar denda tidak nol
                $hariTerlambat = max(1, $selisihHari);
                $nominal       = $hariTerlambat * 5000;

                Denda::create([
                    'id_peminjaman' => $peminjaman->id,
                    'jumlah'        => $nominal,
                    'status'        => StatusDenda::BELUM_DIBAYAR,
                ]);
            });
    }
}
