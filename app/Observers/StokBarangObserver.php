<?php

namespace App\Observers;

use App\Models\StokBarang;

class StokBarangObserver
{
    /**
     * Handle the StokBarang "updated" event.
     */
    public function updated(StokBarang $stokBarang): void
    {
        $barang = $stokBarang->barang;
        if ($barang) {
            $totalStok = $barang->stokBarang()->sum('jumlah');
            
            $barang->updateQuietly([
                'jumlah_total' => $totalStok,
            ]);
        }
    }
}
