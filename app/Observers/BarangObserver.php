<?php

namespace App\Observers;

use App\Models\Barang;
use App\Models\LogBarang;
use App\Enums\KondisiBarang;
use App\Enums\TipeLogBarang;
use App\Services\StokService;

class BarangObserver
{
    protected $stokService;

    public function __construct(StokService $stokService)
    {
        $this->stokService = $stokService;
    }

    /**
     * Handle the Barang "updated" event.
     */
    public function updated(Barang $barang): void
    {
        $changes = [];
        if ($barang->isDirty("nama_barang")) {
            $changes[] =
                "Nama diubah dari '" .
                $barang->getOriginal("nama_barang") .
                "' menjadi '" .
                $barang->nama_barang .
                "'";
        }
        if ($barang->isDirty("keterangan")) {
            $changes[] = "Keterangan diubah";
        }

        if (!empty($changes)) {
            LogBarang::create([
                "id_barang" => $barang->id,
                "kondisi" => KondisiBarang::BAIK,
                "tipe" => TipeLogBarang::MASUK,
                "jumlah" => 0,
                "keterangan" => implode(", ", $changes),
            ]);
        }
    }
}
