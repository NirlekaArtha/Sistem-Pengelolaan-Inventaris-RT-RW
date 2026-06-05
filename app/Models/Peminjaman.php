<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Peminjaman extends Model
{
    use HasFactory;
    protected $fillable = [
        "warga_id",
        "admin_id",
        "tanggal_pinjam",
        "tenggat_pengembalian",
        "tanggal_kembali",
        "status",
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, "admin_id");
    }

    public function detailPeminjamen()
    {
        return $this->hasMany(DetailPeminjaman::class);
    }
}
