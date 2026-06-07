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
                    'id_barang'     => $barang->id,
                    'kondisi'       => $stok['kondisi'],
                    'jumlah_total'  => $stok['jumlah'],
                    'stok_tersedia' => $stok['jumlah'], // akan disesuaikan setelah detail peminjaman dibuat
                ]);
                $totalStok += $stok['jumlah'];
            }

            // Sinkronkan jumlah_total barang dengan total seluruh stok
            $barang->update(['jumlah_total' => $totalStok]);
        });

        // peminjaman
        // peminjaman + detail otomatis yang sinkron dengan stok_tersedia dan jumlah_total secara dinamis
        Peminjaman::factory(20)
            ->create()
            ->each(function (Peminjaman $peminjaman) {
                // Tentukan jumlah barang unik yang dipinjam (antara 1 - 3 barang)
                $numItems = rand(1, 3);
                $selectedStokIds = [];

                for ($i = 0; $i < $numItems; $i++) {
                    // Cari stok barang yang memiliki stok_tersedia > 0 dan belum dipilih dalam peminjaman ini
                    $stokBarang = StokBarang::where('stok_tersedia', '>', 0)
                        ->whereNotIn('id', $selectedStokIds)
                        ->inRandomOrder()
                        ->first();

                    if (!$stokBarang) {
                        break;
                    }

                    $selectedStokIds[] = $stokBarang->id;

                    // Jumlah barang yang dipinjam (antara 1 dan min(5, stok_tersedia))
                    $jumlah = rand(1, min(5, $stokBarang->stok_tersedia));

                    // Kurangi stok_tersedia pada database
                    $stokBarang->stok_tersedia -= $jumlah;

                    $kembaliBaik = 0;
                    $kembaliRusakRingan = 0;
                    $kembaliRusakBerat = 0;

                    // Jika status peminjaman adalah Selesai/Terlambat, hitung logikanya di sini
                    if (in_array($peminjaman->status, [StatusPeminjaman::DIKEMBALIKAN, StatusPeminjaman::DIKEMBALIKAN_TERLAMBAT])) {
                        $kembali = $jumlah;

                        $kembaliBaik = rand(0, $kembali);
                        $kembali -= $kembaliBaik;

                        if ($kembali > 0) {
                            $kembaliRusakRingan = rand(0, $kembali);
                            $kembali -= $kembaliRusakRingan;
                            $kembaliRusakBerat = $kembali;
                        }

                        // Kembalikan ke stok masing-masing kondisi
                        // 1. Kembali Baik
                        if ($kembaliBaik > 0) {
                            $stokBaik = StokBarang::where('id_barang', $stokBarang->id_barang)
                                ->where('kondisi', KondisiBarang::BAIK)
                                ->first();
                            if ($stokBaik) {
                                if ($stokBarang->kondisi === KondisiBarang::BAIK) {
                                    $stokBarang->stok_tersedia += $kembaliBaik;
                                } else {
                                    $stokBarang->jumlah_total -= $kembaliBaik;
                                    $stokBaik->stok_tersedia += $kembaliBaik;
                                    $stokBaik->jumlah_total += $kembaliBaik;
                                    $stokBaik->save();
                                }
                            }
                        }

                        // 2. Kembali Rusak Ringan
                        if ($kembaliRusakRingan > 0) {
                            $stokRusakRingan = StokBarang::where('id_barang', $stokBarang->id_barang)
                                ->where('kondisi', KondisiBarang::RUSAK_RINGAN)
                                ->first();
                            if ($stokRusakRingan) {
                                if ($stokBarang->kondisi === KondisiBarang::RUSAK_RINGAN) {
                                    $stokBarang->stok_tersedia += $kembaliRusakRingan;
                                } else {
                                    $stokBarang->jumlah_total -= $kembaliRusakRingan;
                                    $stokRusakRingan->stok_tersedia += $kembaliRusakRingan;
                                    $stokRusakRingan->jumlah_total += $kembaliRusakRingan;
                                    $stokRusakRingan->save();
                                }
                            }
                        }

                        // 3. Kembali Rusak Berat
                        if ($kembaliRusakBerat > 0) {
                            $stokRusakBerat = StokBarang::where('id_barang', $stokBarang->id_barang)
                                ->where('kondisi', KondisiBarang::RUSAK_BERAT)
                                ->first();
                            if ($stokRusakBerat) {
                                if ($stokBarang->kondisi === KondisiBarang::RUSAK_BERAT) {
                                    $stokBarang->stok_tersedia += $kembaliRusakBerat;
                                } else {
                                    $stokBarang->jumlah_total -= $kembaliRusakBerat;
                                    $stokRusakBerat->stok_tersedia += $kembaliRusakBerat;
                                    $stokRusakBerat->jumlah_total += $kembaliRusakBerat;
                                    $stokRusakBerat->save();
                                }
                            }
                        }
                    }

                    // Simpan sisa perubahan stok asal ke database
                    $stokBarang->save();

                    // Simpan detail peminjaman ke database
                    DetailPeminjaman::create([
                        'id_peminjaman'               => $peminjaman->id,
                        'id_stok_barang'              => $stokBarang->id,
                        'jumlah'                      => $jumlah,
                        'jumlah_kembali_baik'         => $kembaliBaik,
                        'jumlah_kembali_rusak_ringan' => $kembaliRusakRingan,
                        'jumlah_kembali_rusak_berat'  => $kembaliRusakBerat,
                    ]);
                }
            });
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
