<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warga extends Model
{
    use HasFactory;
    protected $fillable = ["nik", "nama", "alamat", "no_hp"];

    public function peminjamen()
    {
        return $this->hasMany(Peminjaman::class);
    }
}
