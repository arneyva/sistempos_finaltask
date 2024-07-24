<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->integer('down_payment_rate')->nullable();
            $table->float('down_payment_net')->nullable();
            $table->dropColumn(['down_payment']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            // Menghapus kolom yang baru ditambahkan
            $table->dropColumn(['address','down_payment_rate','down_payment_net']);
            $table->integer('down_payment')->nullable();
        });
    }
};
