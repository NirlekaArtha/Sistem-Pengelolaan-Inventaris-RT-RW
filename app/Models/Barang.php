<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory;
    protected $fillable = [
        "nama_barang",
        "jumlah_total",
        "jumlah_tersedia",
        "kondisi",
        "lokasi_penyimpanan",
    ];

    public function detailPeminjamen()
    {
        return $this->hasMany(DetailPeminjaman::class);
    }

    public function logBarangs()
    {
        return $this->hasMany(LogBarang::class);
    }
}
