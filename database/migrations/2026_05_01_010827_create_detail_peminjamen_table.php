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
        Schema::create("detail_peminjamen", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("peminjaman_id")
                ->constrained("peminjamen")
                ->cascadeOnDelete();
            $table
                ->foreignId("barang_id")
                ->constrained("barangs")
                ->cascadeOnDelete();
            $table->unsignedInteger("jumlah");
            $table->enum("kondisi_saat_pinjam", [
                "baik",
                "rusak ringan",
                "rusak berat",
            ]);
            $table
                ->enum("kondisi_saat_kembali", [
                    "baik",
                    "rusak ringan",
                    "rusak berat",
                ])
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("detail_peminjamen");
    }
};
