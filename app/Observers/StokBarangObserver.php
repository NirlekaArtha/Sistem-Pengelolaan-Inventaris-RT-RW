<?php

namespace App\Observers;

use App\Models\StokBarang;

class StokBarangObserver
{
    /**
     * Sinkronkan barang.jumlah_total setiap kali stok_barang diperbarui.
     * Menggunakan jumlah_total (fisik) sebagai acuan, bukan stok_tersedia.
     */
    public function updated(StokBarang $stokBarang): void
    {
        $this->syncBarangTotal($stokBarang);
    }

    public function created(StokBarang $stokBarang): void
    {
        $this->syncBarangTotal($stokBarang);
    }

    private function syncBarangTotal(StokBarang $stokBarang): void
    {
        $barang = $stokBarang->barang;
        if ($barang) {
            $totalStok = $barang->stokBarang()->sum('jumlah_total');

            $barang->updateQuietly([
                'jumlah_total' => $totalStok,
            ]);
        }
    }
}
