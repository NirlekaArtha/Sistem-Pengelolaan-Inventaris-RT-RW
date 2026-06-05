<?php

namespace App\Services;

use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\StokBarang;
use App\Enums\StatusPeminjaman;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PeminjamanService
{
    protected $stokService;

    public function __construct(StokService $stokService)
    {
        $this->stokService = $stokService;
    }

    /**
     * Membuat transaksi peminjaman baru dalam satu DB transaction.
     *
     * @param array $data Data peminjaman (id_warga, id_admin, tanggal_pinjam, tenggat_pengembalian, status)
     * @param array $details List detail barang (id_stok_barang, jumlah)
     * @return Peminjaman
     */
    public function buatPeminjaman(array $data, array $details): Peminjaman
    {
        if (empty($details)) {
            throw new InvalidArgumentException("Peminjaman harus menyertakan minimal satu barang.");
        }

        return DB::transaction(function () use ($data, $details) {
            // 1. Validasi kecukupan stok untuk semua barang di details
            foreach ($details as $detail) {
                $stok = StokBarang::lockForUpdate()->find($detail['id_stok_barang']);
                if (!$stok) {
                    throw new InvalidArgumentException("Stok barang tidak ditemukan.");
                }
                
                if (!$this->stokService->cekKetersediaan($stok, $detail['jumlah'])) {
                    throw new InvalidArgumentException(
                        "Stok barang '{$stok->barang->nama_barang}' (kondisi: {$stok->kondisi->value}) tidak mencukupi. Tersedia: {$stok->jumlah}, Diminta: {$detail['jumlah']}."
                    );
                }
            }

            // 2. Buat record Peminjaman
            $data['status'] = $data['status'] ?? StatusPeminjaman::DIPINJAM;
            $peminjaman = Peminjaman::create($data);

            // 3. Simpan Detail Peminjaman & Kurangi Stok
            foreach ($details as $detail) {
                $stok = StokBarang::lockForUpdate()->find($detail['id_stok_barang']);
                
                // Simpan record detail_peminjaman
                DetailPeminjaman::create([
                    'id_peminjaman' => $peminjaman->id,
                    'id_stok_barang' => $stok->id,
                    'jumlah' => $detail['jumlah'],
                    'kondisi_kembali' => null,
                    'jumlah_kembali_baik' => 0,
                    'jumlah_kembali_rusak_ringan' => 0,
                    'jumlah_kembali_rusak_berat' => 0,
                ]);

                // Kurangi stok barang + catat log keluar
                $keteranganLog = "Peminjaman #{$peminjaman->id} oleh warga ID {$peminjaman->id_warga}";
                $this->stokService->kurangiStok($stok, $detail['jumlah'], $keteranganLog);
            }

            return $peminjaman;
        });
    }
}
