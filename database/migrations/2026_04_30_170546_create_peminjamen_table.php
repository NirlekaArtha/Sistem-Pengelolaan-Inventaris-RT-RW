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
        Schema::create("peminjamen", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("warga_id")
                ->constrained("wargas")
                ->cascadeOnDelete();
            $table
                ->foreignId("admin_id")
                ->constrained("users")
                ->cascadeOnDelete();
            $table->date("tanggal_pinjam");
            $table->date("tenggat_pengembalian");
            $table->date("tanggal_kembali")->nullable();
            $table->enum("status", ["dipinjam", "dikembalikan", "terlambat"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("peminjamen");
    }
};
