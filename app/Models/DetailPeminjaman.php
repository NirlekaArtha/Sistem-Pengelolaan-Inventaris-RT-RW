<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPeminjaman extends Model
{
    use HasFactory;

    protected $table = 'detail_peminjaman';

    protected $fillable = [
        'id_peminjaman',
        'id_stok_barang',
        'jumlah',
        'jumlah_kembali_baik',
        'jumlah_kembali_rusak_ringan',
        'jumlah_kembali_rusak_berat',
    ];

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class, 'id_peminjaman');
    }

    public function stokBarang(): BelongsTo
    {
        return $this->belongsTo(StokBarang::class, 'id_stok_barang');
    }
}
