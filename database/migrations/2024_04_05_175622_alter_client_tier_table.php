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
        if (Schema::hasTable('client_tiers')) {
            Schema::table('client_tiers', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'total_sales')) {
                    $table->truncate();
                    $table->integer('total_sales');
                }
                if (Schema::hasColumn($table->getTable(), 'total_sales')) {
                    $table->truncate();
                    $table->dropColumn('total_amount');
                }
                if (Schema::hasColumn($table->getTable(), 'total_sales')) {
                    $table->truncate();
                    $table->dropColumn('last_sale');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('client_tiers')) {
            Schema::table('client_tiers', function (Blueprint $table) {
                if (! Schema::hasColumn($table->getTable(), 'total_sales')) {
                    $table->truncate();
                    $table->integer('total_sales');
                }
                if (! Schema::hasColumn($table->getTable(), 'total_amount')) {
                    $table->truncate();
                    $table->float('total_amount', 10, 0);
                }
                if (! Schema::hasColumn($table->getTable(), 'last_sale')) {
                    $table->truncate();
                    $table->integer('last_sale');
                }
            });
        }
    }
};
