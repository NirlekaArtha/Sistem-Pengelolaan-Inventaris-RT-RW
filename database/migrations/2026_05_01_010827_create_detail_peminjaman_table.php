<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_peminjaman')->constrained('peminjaman')->cascadeOnDelete();
            $table->foreignId('id_stok_barang')->constrained('stok_barang')->cascadeOnDelete();
            $table->unsignedInteger('jumlah');
            $table->enum('kondisi_kembali', ['baik', 'rusak_ringan', 'rusak_berat'])->nullable();
            $table->unsignedInteger('jumlah_kembali_baik')->default(0);
            $table->unsignedInteger('jumlah_kembali_rusak_ringan')->default(0);
            $table->unsignedInteger('jumlah_kembali_rusak_berat')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_peminjaman');
    }
};
