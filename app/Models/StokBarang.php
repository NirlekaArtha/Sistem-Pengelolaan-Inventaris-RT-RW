<?php

namespace App\Models;

use App\Enums\KondisiBarang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokBarang extends Model
{
    use HasFactory;

    protected $table = 'stok_barang';

    protected $fillable = [
        'id_barang',
        'kondisi',
        'jumlah_total',
        'stok_tersedia',
    ];

    protected $casts = [
        'kondisi' => KondisiBarang::class,
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}
