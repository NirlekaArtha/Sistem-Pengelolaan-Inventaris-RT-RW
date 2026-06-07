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
     * @param array $data    Data peminjaman (id_warga, id_admin, tanggal_pinjam, tenggat_pengembalian, status)
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
                        "Stok barang '{$stok->barang->nama_barang}' (kondisi: {$stok->kondisi->value}) tidak mencukupi. Tersedia: {$stok->stok_tersedia}, Diminta: {$detail['jumlah']}."
                    );
                }
            }

            // 2. Buat record Peminjaman
            $data['status'] = $data['status'] ?? StatusPeminjaman::DIPINJAM;
            $peminjaman = Peminjaman::create($data);

            // 3. Simpan Detail Peminjaman & Kurangi Stok Tersedia
            foreach ($details as $detail) {
                $stok = StokBarang::lockForUpdate()->find($detail['id_stok_barang']);

                DetailPeminjaman::create([
                    'id_peminjaman'               => $peminjaman->id,
                    'id_stok_barang'              => $stok->id,
                    'jumlah'                      => $detail['jumlah'],
                    'jumlah_kembali_baik'         => 0,
                    'jumlah_kembali_rusak_ringan' => 0,
                    'jumlah_kembali_rusak_berat'  => 0,
                ]);

                $keteranganLog = "Peminjaman #{$peminjaman->id} oleh warga ID {$peminjaman->id_warga}";
                $this->stokService->pinjamStok($stok, $detail['jumlah'], $keteranganLog);
            }

            return $peminjaman;
        });
    }

    /**
     * Update peminjaman: perbarui header + rekonsiliasi detail dengan penyesuaian stok.
     *
     * Logika rekonsiliasi:
     * - Item lama yang dihapus dari form → stok dikembalikan, detail dihapus
     * - Item baru ditambahkan ke form    → stok dikurangi, detail dibuat
     * - Item sama, jumlah berubah        → stok disesuaikan dengan selisih
     * - Item diganti (stok berbeda)      → stok lama dikembalikan, stok baru dikurangi
     *
     * @param Peminjaman $peminjaman
     * @param array      $data       Header data (warga, tanggal, dll)
     * @param array      $newDetails Detail baru dari form repeater
     * @return Peminjaman
     */
    public function updatePeminjaman(Peminjaman $peminjaman, array $data, array $newDetails): Peminjaman
    {
        if (empty($newDetails)) {
            throw new InvalidArgumentException("Peminjaman harus menyertakan minimal satu barang.");
        }

        return DB::transaction(function () use ($peminjaman, $data, $newDetails) {
            // 1. Update header peminjaman
            $peminjaman->update($data);

            // 2. Muat detail lama dari DB, di-index berdasarkan id_stok_barang
            $oldDetails = $peminjaman->detailPeminjaman->keyBy('id_stok_barang');

            $processedStokIds = [];

            // 3. Proses setiap detail dari form
            foreach ($newDetails as $newDetail) {
                $idStokBarang = (int) $newDetail['id_stok_barang'];
                $newJumlah    = (int) $newDetail['jumlah'];

                if ($oldDetails->has($idStokBarang)) {
                    // Item yang sama sudah ada — cek apakah jumlah berubah
                    $oldDetail = $oldDetails->get($idStokBarang);
                    $oldJumlah = (int) $oldDetail->jumlah;

                    if ($newJumlah !== $oldJumlah) {
                        $stok = StokBarang::lockForUpdate()->find($idStokBarang);

                        if ($newJumlah > $oldJumlah) {
                            // Jumlah bertambah → kurangi stok_tersedia tambahan
                            $diff = $newJumlah - $oldJumlah;
                            if (!$this->stokService->cekKetersediaan($stok, $diff)) {
                                throw new InvalidArgumentException(
                                    "Stok '{$stok->barang->nama_barang}' tidak mencukupi. " .
                                    "Tersedia: {$stok->stok_tersedia}, Tambahan diminta: {$diff}."
                                );
                            }
                            $this->stokService->pinjamStok(
                                $stok,
                                $diff,
                                "Edit Peminjaman #{$peminjaman->id} - tambah jumlah"
                            );
                        } else {
                            // Jumlah berkurang → kembalikan selisih ke stok_tersedia
                            $diff = $oldJumlah - $newJumlah;
                            $this->stokService->kembalikanStok(
                                $stok,
                                $stok,
                                $diff,
                                "Edit Peminjaman #{$peminjaman->id} - kurangi jumlah"
                            );
                        }

                        $oldDetail->update(['jumlah' => $newJumlah]);
                    }

                    $processedStokIds[] = $idStokBarang;
                } else {
                    // Item baru (barang/kondisi diganti atau ditambah)
                    $stok = StokBarang::lockForUpdate()->find($idStokBarang);
                    if (!$stok) {
                        throw new InvalidArgumentException("Stok barang tidak ditemukan.");
                    }
                    if (!$this->stokService->cekKetersediaan($stok, $newJumlah)) {
                        throw new InvalidArgumentException(
                            "Stok '{$stok->barang->nama_barang}' tidak mencukupi. " .
                            "Tersedia: {$stok->stok_tersedia}, Diminta: {$newJumlah}."
                        );
                    }

                    $this->stokService->pinjamStok(
                        $stok,
                        $newJumlah,
                        "Edit Peminjaman #{$peminjaman->id} - barang baru/diganti"
                    );

                    DetailPeminjaman::create([
                        'id_peminjaman'               => $peminjaman->id,
                        'id_stok_barang'              => $idStokBarang,
                        'jumlah'                      => $newJumlah,
                        'jumlah_kembali_baik'         => 0,
                        'jumlah_kembali_rusak_ringan' => 0,
                        'jumlah_kembali_rusak_berat'  => 0,
                    ]);

                    $processedStokIds[] = $idStokBarang;
                }
            }

            // 4. Detail yang tidak ada di form baru → kembalikan stok_tersedia & hapus
            foreach ($oldDetails as $idStokBarang => $oldDetail) {
                if (!in_array($idStokBarang, $processedStokIds)) {
                    $stok = StokBarang::find($idStokBarang);
                    if ($stok) {
                        $this->stokService->kembalikanStok(
                            $stok,
                            $stok,
                            (int) $oldDetail->jumlah,
                            "Edit Peminjaman #{$peminjaman->id} - barang dihapus/diganti"
                        );
                    }
                    $oldDetail->delete();
                }
            }

            return $peminjaman->fresh();
        });
    }
}
