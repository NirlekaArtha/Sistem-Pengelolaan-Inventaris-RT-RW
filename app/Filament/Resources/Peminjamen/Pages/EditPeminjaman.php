<?php

namespace App\Filament\Resources\Peminjamen\Pages;

use App\Filament\Resources\Peminjamen\PeminjamanResource;
use App\Models\DetailPeminjaman;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class EditPeminjaman extends EditRecord
{
    protected static string $resource = PeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->icon("heroicon-o-eye"),
            DeleteAction::make()->icon("heroicon-o-trash"),
        ];
    }

    /**
     * Isi form dengan data yang ada, termasuk Repeater detailPeminjaman
     * yang tidak menggunakan ->relationship() sehingga harus diisi manual.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        $data['detailPeminjaman'] = $record->detailPeminjaman
            ->map(function (DetailPeminjaman $detail) {
                $stok = $detail->stokBarang;
                return [
                    'id_barang'      => $stok?->id_barang,
                    'kondisi'        => $stok?->kondisi instanceof \BackedEnum
                                            ? $stok->kondisi->value
                                            : $stok?->kondisi,
                    'id_stok_barang' => $detail->id_stok_barang,
                    'stok_tersedia'  => ($stok?->stok_tersedia ?? 0) + $detail->jumlah,
                    'jumlah'         => $detail->jumlah,
                ];
            })
            ->values()
            ->toArray();

        return $data;
    }

    /**
     * Simpan perubahan: update header + rekonsiliasi detail + penyesuaian stok
     * via PeminjamanService::updatePeminjaman.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $newDetails = $data['detailPeminjaman'] ?? [];
        unset($data['detailPeminjaman']);

        /** @var \App\Services\PeminjamanService $service */
        $service = app(\App\Services\PeminjamanService::class);

        return $service->updatePeminjaman($record, $data, $newDetails);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title("Peminjaman Diperbarui")
            ->body("Data peminjaman berhasil diperbarui.");
    }
}
