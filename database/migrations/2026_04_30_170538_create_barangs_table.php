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
        Schema::create("barangs", function (Blueprint $table) {
            $table->id();
            $table->string("nama_barang");
            $table->integer("jumlah_total");
            $table->integer("jumlah_tersedia");
            $table->enum("kondisi", ["baik", "rusak ringan", "rusak berat"]);
            $table->string("lokasi_penyimpanan");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("barangs");
    }
};
