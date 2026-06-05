<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'nama_barang',
        'keterangan',
        'jumlah_total',
    ];

    public function stokBarang(): HasMany
    {
        return $this->hasMany(StokBarang::class, 'id_barang');
    }

    public function logBarang(): HasMany
    {
        return $this->hasMany(LogBarang::class, 'id_barang');
    }
}
