<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class LogBarang extends Model
{
    use HasFactory;
    protected $fillable = ["barang_id", "admin_id", "tipe", "jumlah"];

    public function barang()
    {
        return $this->belongsTo(Barang::class, "barang_id");
    }

    public function admin()
    {
        return $this->belongsTo(User::class, "admin_id");
    }
}
