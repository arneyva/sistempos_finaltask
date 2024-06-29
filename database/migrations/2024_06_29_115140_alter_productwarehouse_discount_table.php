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
        Schema::table('product_warehouse', function (Blueprint $table) {
            $table->unsignedInteger('quantity_discount')->nullable()->after('qty');
            $table->float('discount_percentage', 10,)->nullable()->after('quantity_discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_warehouse', function (Blueprint $table) {
            $table->dropColumn('quantity_discount');
            $table->dropColumn('discount_percentage');
        });
    }
};
