<?php

namespace App\Services;

use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\StokBarang;
use App\Models\Denda;
use App\Enums\KondisiBarang;
use App\Enums\StatusPeminjaman;
use App\Enums\StatusDenda;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Carbon\Carbon;

class PengembalianService
{
    protected $stokService;

    public function __construct(StokService $stokService)
    {
        $this->stokService = $stokService;
    }

    /**
     * Memproses pengembalian barang pinjaman dalam satu DB transaction.
     *
     * @param Peminjaman $peminjaman
     * @param array $kondisiKembali Associative array of [detail_id => ['baik' => int, 'rusak_ringan' => int, 'rusak_berat' => int]]
     * @param string|null $tanggalKembaliStr Tanggal kembali kustom (untuk testing/keperluan khusus)
     * @return Peminjaman
     */
    public function prosesKembali(Peminjaman $peminjaman, array $kondisiKembali, ?string $tanggalKembaliStr = null): Peminjaman
    {
        return DB::transaction(function () use ($peminjaman, $kondisiKembali, $tanggalKembaliStr) {
            $tanggalKembali = $tanggalKembaliStr ? Carbon::parse($tanggalKembaliStr) : Carbon::today();

            foreach ($peminjaman->detailPeminjaman as $detail) {
                $detailInput = $kondisiKembali[$detail->id] ?? [
                    'baik' => 0,
                    'rusak_ringan' => 0,
                    'rusak_berat' => 0,
                ];

                $baik = (int) ($detailInput['baik'] ?? 0);
                $rusakRingan = (int) ($detailInput['rusak_ringan'] ?? 0);
                $rusakBerat = (int) ($detailInput['rusak_berat'] ?? 0);

                $totalKembali = $baik + $rusakRingan + $rusakBerat;

                if ($totalKembali > $detail->jumlah) {
                    throw new InvalidArgumentException(
                        "Total barang yang dikembalikan ({$totalKembali}) melebihi jumlah yang dipinjam ({$detail->jumlah}) untuk item detail ID {$detail->id}."
                    );
                }

                // Tentukan kondisi_kembali dominan/terburuk
                $kondisi = KondisiBarang::BAIK;
                if ($rusakBerat > 0) {
                    $kondisi = KondisiBarang::RUSAK_BERAT;
                } elseif ($rusakRingan > 0) {
                    $kondisi = KondisiBarang::RUSAK_RINGAN;
                }

                // Update detail record
                $detail->update([
                    'kondisi_kembali' => $kondisi,
                    'jumlah_kembali_baik' => $baik,
                    'jumlah_kembali_rusak_ringan' => $rusakRingan,
                    'jumlah_kembali_rusak_berat' => $rusakBerat,
                ]);

                // Update masing-masing stok kondisi
                $idBarang = $detail->stokBarang->id_barang;
                $keteranganLog = "Pengembalian #{$peminjaman->id} oleh warga ID {$peminjaman->id_warga}";

                if ($baik > 0) {
                    $stokBaik = StokBarang::where('id_barang', $idBarang)->where('kondisi', KondisiBarang::BAIK)->first();
                    $this->stokService->tambahStok($stokBaik, $baik, $keteranganLog);
                }
                if ($rusakRingan > 0) {
                    $stokRusakRingan = StokBarang::where('id_barang', $idBarang)->where('kondisi', KondisiBarang::RUSAK_RINGAN)->first();
                    $this->stokService->tambahStok($stokRusakRingan, $rusakRingan, $keteranganLog);
                }
                if ($rusakBerat > 0) {
                    $stokRusakBerat = StokBarang::where('id_barang', $idBarang)->where('kondisi', KondisiBarang::RUSAK_BERAT)->first();
                    $this->stokService->tambahStok($stokRusakBerat, $rusakBerat, $keteranganLog);
                }
            }

            // Update Peminjaman
            $peminjaman->tanggal_kembali = $tanggalKembali;

            // Cek keterlambatan
            $tenggat = Carbon::parse($peminjaman->tenggat_pengembalian);
            $isTerlambat = $tanggalKembali->startOfDay()->gt($tenggat->startOfDay());

            if ($isTerlambat) {
                $peminjaman->status = StatusPeminjaman::DIKEMBALIKAN_TERLAMBAT;

                // Kalkulasi denda: Rp 5.000 per hari keterlambatan
                $daysLate = (int) $tanggalKembali->startOfDay()->diffInDays($tenggat->startOfDay(), true);
                $jumlahDenda = max(1, $daysLate) * 5000;

                Denda::create([
                    'id_peminjaman' => $peminjaman->id,
                    'jumlah' => $jumlahDenda,
                    'status' => StatusDenda::BELUM_DIBAYAR,
                ]);
            } else {
                $peminjaman->status = StatusPeminjaman::DIKEMBALIKAN;
            }

            $peminjaman->save();

            return $peminjaman;
        });
    }
}
