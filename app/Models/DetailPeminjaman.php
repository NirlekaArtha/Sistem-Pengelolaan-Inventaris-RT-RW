<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailPeminjaman extends Model
{
    use HasFactory;
    protected $fillable = [
        "peminjaman_id",
        "barang_id",
        "jumlah",
        "kondisi_saat_pinjam",
        "kondisi_saat_kembali",
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
