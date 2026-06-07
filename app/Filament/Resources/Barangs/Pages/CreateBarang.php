<?php

namespace App\Filament\Resources\Barangs\Pages;

use App\Enums\KondisiBarang;
use App\Enums\TipeLogBarang;
use App\Filament\Resources\Barangs\BarangResource;
use App\Models\LogBarang;
use App\Models\StokBarang;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateBarang extends CreateRecord
{
    protected static string $resource = BarangResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Extract stok fields, not stored in barang table
        $stokBaik = (int) ($data["stok_baik"] ?? 0);
        $stokRusakRingan = (int) ($data["stok_rusak_ringan"] ?? 0);
        $stokRusakBerat = (int) ($data["stok_rusak_berat"] ?? 0);

        $jumlahTotal = $stokBaik + $stokRusakRingan + $stokRusakBerat;

        return DB::transaction(function () use (
            $data,
            $stokBaik,
            $stokRusakRingan,
            $stokRusakBerat,
            $jumlahTotal,
        ) {
            /** @var \App\Models\Barang $barang */
            $barang = static::getModel()::create([
                "nama_barang" => $data["nama_barang"],
                "keterangan" => $data["keterangan"] ?? null,
                "jumlah_total" => $jumlahTotal,
            ]);

            // FIX: Use ->value for the array keys
            $stokList = [
                KondisiBarang::BAIK->value => $stokBaik,
                KondisiBarang::RUSAK_RINGAN->value => $stokRusakRingan,
                KondisiBarang::RUSAK_BERAT->value => $stokRusakBerat,
            ];

            foreach ($stokList as $kondisi => $jumlah) {
                $stok = StokBarang::updateOrCreate([
                    "id_barang" => $barang->id,
                    "kondisi"   => $kondisi,
                ], [
                    "jumlah_total"   => $jumlah,
                    "stok_tersedia"  => $jumlah, // stok_tersedia = jumlah_total saat pertama dibuat
                ]);

                // Catat log masuk jika ada stok awal
                if ($jumlah > 0) {
                    LogBarang::create([
                        "id_barang" => $barang->id,
                        "kondisi" => $kondisi,
                        "tipe" => TipeLogBarang::MASUK, // Eloquent handles Enum instances fine here if casted in Model
                        "jumlah" => $jumlah,
                        "keterangan" => "Stok awal saat pendaftaran barang",
                    ]);
                }
            }

            return $barang;
        });
    }
}
