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
     * Inisialisasi baris stok (jumlah_total=0, stok_tersedia=0) untuk setiap kondisi.
     */
    public function inisialisasiStok(Barang $barang): void
    {
        $kondisiList = [
            KondisiBarang::BAIK,
            KondisiBarang::RUSAK_RINGAN,
            KondisiBarang::RUSAK_BERAT,
        ];

        foreach ($kondisiList as $kondisi) {
            StokBarang::firstOrCreate(
                ['id_barang' => $barang->id, 'kondisi' => $kondisi],
                ['jumlah_total' => 0, 'stok_tersedia' => 0]
            );
        }
    }

    /**
     * Admin menambah fisik inventory: jumlah_total++ dan stok_tersedia++, catat log MASUK.
     */
    public function tambahStok(StokBarang $stok, int $jumlah, ?string $keterangan = null): void
    {
        if ($jumlah <= 0) {
            throw new InvalidArgumentException("Jumlah penambahan harus lebih dari 0.");
        }

        DB::transaction(function () use ($stok, $jumlah, $keterangan) {
            $stok->increment('jumlah_total', $jumlah);
            $stok->increment('stok_tersedia', $jumlah);

            LogBarang::create([
                'id_barang'  => $stok->id_barang,
                'kondisi'    => $stok->kondisi,
                'tipe'       => TipeLogBarang::MASUK,
                'jumlah'     => $jumlah,
                'keterangan' => $keterangan,
            ]);
        });
    }

    /**
     * Admin mengurangi fisik inventory: jumlah_total-- dan stok_tersedia--, catat log KELUAR.
     */
    public function kurangiStok(StokBarang $stok, int $jumlah, ?string $keterangan = null): void
    {
        if ($jumlah <= 0) {
            throw new InvalidArgumentException("Jumlah pengurangan harus lebih dari 0.");
        }

        if ($stok->stok_tersedia < $jumlah) {
            throw new InvalidArgumentException("Stok tersedia tidak mencukupi untuk dikurangi.");
        }

        DB::transaction(function () use ($stok, $jumlah, $keterangan) {
            $stok->decrement('jumlah_total', $jumlah);
            $stok->decrement('stok_tersedia', $jumlah);

            LogBarang::create([
                'id_barang'  => $stok->id_barang,
                'kondisi'    => $stok->kondisi,
                'tipe'       => TipeLogBarang::KELUAR,
                'jumlah'     => $jumlah,
                'keterangan' => $keterangan,
            ]);
        });
    }

    /**
     * Proses PEMINJAMAN: hanya mengurangi stok_tersedia (jumlah_total fisik tidak berubah).
     * Catat log KELUAR.
     */
    public function pinjamStok(StokBarang $stok, int $jumlah, ?string $keterangan = null): void
    {
        if ($jumlah <= 0) {
            throw new InvalidArgumentException("Jumlah peminjaman harus lebih dari 0.");
        }

        if (!$this->cekKetersediaan($stok, $jumlah)) {
            throw new InvalidArgumentException(
                "Stok tersedia '{$stok->barang->nama_barang}' (kondisi: {$stok->kondisi->value}) tidak mencukupi. " .
                "Tersedia: {$stok->stok_tersedia}, Diminta: {$jumlah}."
            );
        }

        DB::transaction(function () use ($stok, $jumlah, $keterangan) {
            $stok->decrement('stok_tersedia', $jumlah);

            LogBarang::create([
                'id_barang'  => $stok->id_barang,
                'kondisi'    => $stok->kondisi,
                'tipe'       => TipeLogBarang::KELUAR,
                'jumlah'     => $jumlah,
                'keterangan' => $keterangan,
            ]);
        });
    }

    /**
     * Proses PENGEMBALIAN: tambah stok_tersedia pada stok tujuan (kondisi kembali).
     * Jika kondisi kembali BERBEDA dari kondisi asal, sesuaikan jumlah_total antar kondisi
     * karena item secara fisik berpindah kondisi.
     * Catat log MASUK.
     *
     * @param StokBarang $originalStok  Stok yang dipinjam (kondisi asal)
     * @param StokBarang $returnStok    Stok tujuan kembali (bisa sama atau berbeda kondisi)
     * @param int        $jumlah
     * @param string|null $keterangan
     */
    public function kembalikanStok(
        StokBarang $originalStok,
        StokBarang $returnStok,
        int $jumlah,
        ?string $keterangan = null
    ): void {
        if ($jumlah <= 0) {
            throw new InvalidArgumentException("Jumlah pengembalian harus lebih dari 0.");
        }

        DB::transaction(function () use ($originalStok, $returnStok, $jumlah, $keterangan) {
            // Tambah stok_tersedia pada kondisi kembali
            $returnStok->increment('stok_tersedia', $jumlah);

            // Jika kondisi berbeda, item berpindah kondisi secara fisik
            if ($originalStok->kondisi->value !== $returnStok->kondisi->value) {
                $originalStok->decrement('jumlah_total', $jumlah);
                $returnStok->increment('jumlah_total', $jumlah);
            }

            LogBarang::create([
                'id_barang'  => $returnStok->id_barang,
                'kondisi'    => $returnStok->kondisi,
                'tipe'       => TipeLogBarang::MASUK,
                'jumlah'     => $jumlah,
                'keterangan' => $keterangan,
            ]);
        });
    }

    /**
     * Cek apakah stok_tersedia mencukupi untuk dipinjam.
     */
    public function cekKetersediaan(StokBarang $stok, int $jumlah): bool
    {
        return $stok->fresh()->stok_tersedia >= $jumlah;
    }
}
