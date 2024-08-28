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
        Schema::table('purchase_return_details', function (Blueprint $table) {
            // Menambahkan kolom baru
            $table->renameColumn('quantity', 'qty_unpassed');
            $table->float('qty_return')->nullable();
            $table->float('qty_request')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_return_details', function (Blueprint $table) {
            $table->dropColumn(['qty_return','qty_request']);
            $table->renameColumn('qty_unpassed', 'quantity');
        });
    }
};
