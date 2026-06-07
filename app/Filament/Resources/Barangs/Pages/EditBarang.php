<?php

namespace App\Filament\Resources\Barangs\Pages;

use App\Enums\KondisiBarang;
use App\Enums\TipeLogBarang;
use App\Filament\Resources\Barangs\BarangResource;
use App\Models\LogBarang;
use App\Models\StokBarang;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditBarang extends EditRecord
{
    protected static string $resource = BarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * Pre-populate the stok fields from the stokBarang relation.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $barang = $this->getRecord();

        $data['stok_baik']         = $barang->stokBarang()->where('kondisi', KondisiBarang::BAIK)->value('jumlah_total') ?? 0;
        $data['stok_rusak_ringan'] = $barang->stokBarang()->where('kondisi', KondisiBarang::RUSAK_RINGAN)->value('jumlah_total') ?? 0;
        $data['stok_rusak_berat']  = $barang->stokBarang()->where('kondisi', KondisiBarang::RUSAK_BERAT)->value('jumlah_total') ?? 0;

        return $data;
    }

    /**
     * Save stok changes and log diffs.
     */
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $stokBaru = [
            KondisiBarang::BAIK->value => (int) ($data['stok_baik'] ?? 0),
            KondisiBarang::RUSAK_RINGAN->value => (int) ($data['stok_rusak_ringan'] ?? 0),
            KondisiBarang::RUSAK_BERAT->value => (int) ($data['stok_rusak_berat'] ?? 0),
        ];

        $jumlahTotal = array_sum($stokBaru);

        DB::transaction(function () use ($record, $data, $stokBaru, $jumlahTotal) {
            $record->update([
                'nama_barang'  => $data['nama_barang'],
                'keterangan'   => $data['keterangan'] ?? null,
                'jumlah_total' => $jumlahTotal,
            ]);

            foreach ($stokBaru as $kondisi => $jumlahTotalBaru) {
                $stok = StokBarang::firstOrCreate(
                    ['id_barang' => $record->id, 'kondisi' => $kondisi],
                    ['jumlah_total' => 0, 'stok_tersedia' => 0]
                );

                $jumlahTotalLama = $stok->jumlah_total;
                $selisih         = $jumlahTotalBaru - $jumlahTotalLama;

                if ($selisih === 0) continue;

                // Sesuaikan stok_tersedia dengan selisih yang sama
                // (admin menambah/mengurangi unit fisik, ketersediaan ikut berubah)
                $stokTersediaBaru = max(0, $stok->stok_tersedia + $selisih);

                $stok->update([
                    'jumlah_total'  => $jumlahTotalBaru,
                    'stok_tersedia' => $stokTersediaBaru,
                ]);

                // Catat log perubahan stok
                LogBarang::create([
                    'id_barang'  => $record->id,
                    'kondisi'    => $kondisi,
                    'tipe'       => $selisih > 0 ? TipeLogBarang::MASUK : TipeLogBarang::KELUAR,
                    'jumlah'     => abs($selisih),
                    'keterangan' => 'Penyesuaian stok melalui form edit barang',
                ]);
            }
        });

        return $record->fresh();
    }
}
