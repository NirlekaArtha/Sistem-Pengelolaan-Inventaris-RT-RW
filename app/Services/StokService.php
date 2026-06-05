<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\StokBarang;
use App\Models\LogBarang;
use App\Enums\KondisiBarang;
use App\Enums\TipeLogBarang;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StokService
{
    /**
     * Inisialisasi stok awal (baik, rusak_ringan, rusak_berat) dengan jumlah 0 untuk barang baru.
     */
    public function inisialisasiStok(Barang $barang): void
    {
        $kondisiList = [
            KondisiBarang::BAIK,
            KondisiBarang::RUSAK_RINGAN,
            KondisiBarang::RUSAK_BERAT,
        ];

        foreach ($kondisiList as $kondisi) {
            StokBarang::firstOrCreate([
                'id_barang' => $barang->id,
                'kondisi' => $kondisi,
            ], [
                'jumlah' => 0,
            ]);
        }
    }

    /**
     * Tambah jumlah stok dan catat log masuk.
     */
    public function tambahStok(StokBarang $stok, int $jumlah, ?string $keterangan = null): void
    {
        if ($jumlah <= 0) {
            throw new InvalidArgumentException("Jumlah penambahan harus lebih dari 0.");
        }

        DB::transaction(function () use ($stok, $jumlah, $keterangan) {
            $stok->increment('jumlah', $jumlah);

            LogBarang::create([
                'id_barang' => $stok->id_barang,
                'kondisi' => $stok->kondisi,
                'tipe' => TipeLogBarang::MASUK,
                'jumlah' => $jumlah,
                'keterangan' => $keterangan,
            ]);
        });
    }

    /**
     * Kurangi jumlah stok (jika cukup) dan catat log keluar.
     */
    public function kurangiStok(StokBarang $stok, int $jumlah, ?string $keterangan = null): void
    {
        if ($jumlah <= 0) {
            throw new InvalidArgumentException("Jumlah pengurangan harus lebih dari 0.");
        }

        if (!$this->cekKetersediaan($stok, $jumlah)) {
            throw new InvalidArgumentException("Stok tidak mencukupi untuk melakukan peminjaman.");
        }

        DB::transaction(function () use ($stok, $jumlah, $keterangan) {
            $stok->decrement('jumlah', $jumlah);

            LogBarang::create([
                'id_barang' => $stok->id_barang,
                'kondisi' => $stok->kondisi,
                'tipe' => TipeLogBarang::KELUAR,
                'jumlah' => $jumlah,
                'keterangan' => $keterangan,
            ]);
        });
    }

    /**
     * Cek apakah stok mencukupi.
     */
    public function cekKetersediaan(StokBarang $stok, int $jumlah): bool
    {
        return $stok->jumlah >= $jumlah;
    }
}
