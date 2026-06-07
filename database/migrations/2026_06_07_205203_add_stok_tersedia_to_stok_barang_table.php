<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_barang', function (Blueprint $table) {
            $table->renameColumn('jumlah', 'jumlah_total');
        });

        Schema::table('stok_barang', function (Blueprint $table) {
            $table->unsignedInteger('stok_tersedia')->default(0)->after('jumlah_total');
        });

        // Inisialisasi stok_tersedia = jumlah_total - jumlah yang sedang aktif dipinjam
        DB::statement("
            UPDATE stok_barang
            SET stok_tersedia = stok_barang.jumlah_total - COALESCE((
                SELECT SUM(dp.jumlah)
                FROM detail_peminjaman dp
                INNER JOIN peminjaman p ON dp.id_peminjaman = p.id
                WHERE dp.id_stok_barang = stok_barang.id
                  AND p.status IN ('dipinjam', 'terlambat')
            ), 0)
        ");
    }

    public function down(): void
    {
        Schema::table('stok_barang', function (Blueprint $table) {
            $table->dropColumn('stok_tersedia');
        });

        Schema::table('stok_barang', function (Blueprint $table) {
            $table->renameColumn('jumlah_total', 'jumlah');
        });
    }
};
