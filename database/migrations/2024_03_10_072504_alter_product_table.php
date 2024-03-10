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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock_alert');
        });
        Schema::table('product_warehouse', function (Blueprint $table) {
            $table->dropColumn('qte');
            $table->double('stock_alert')->nullable()->default(0)->after('manage_stock');
            $table->double('qty')->nullable()->default(0)->after('stock_alert');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->double('stock_alert')->nullable()->default(0);
        });
        Schema::table('product_warehouse', function (Blueprint $table) {
            $table->double('qte')->after('manage_stock');
            $table->dropColumn('stock_alert')->nullable()->default(0);
            $table->dropColumn('qty')->nullable()->default(0);
        });
    }
};
