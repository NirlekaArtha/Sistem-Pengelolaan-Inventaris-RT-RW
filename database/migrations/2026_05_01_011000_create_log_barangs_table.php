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
        Schema::create("log_barangs", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("barang_id")
                ->constrained("barangs")
                ->cascadeOnDelete();
            $table
                ->foreignId("admin_id")
                ->constrained("users")
                ->cascadeOnDelete();
            $table->enum("tipe", ["masuk", "keluar"]);
            $table->unsignedInteger("jumlah");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("log_barangs");
    }
};
