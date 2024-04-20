<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
                    DB::table('client_tiers')->delete();
                    $table->dropColumn('total_sales');
                }
                if (Schema::hasColumn($table->getTable(), 'total_sales')) {
                    DB::table('client_tiers')->delete();
                    $table->dropColumn('total_amount');
                }
                if (Schema::hasColumn($table->getTable(), 'total_sales')) {
                    DB::table('client_tiers')->delete();
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
                    DB::table('client_tiers')->delete();
                    $table->integer('total_sales');
                }
                if (! Schema::hasColumn($table->getTable(), 'total_amount')) {
                    DB::table('client_tiers')->delete();
                    $table->float('total_amount', 10, 0);
                }
                if (! Schema::hasColumn($table->getTable(), 'last_sale')) {
                    DB::table('client_tiers')->delete();
                    $table->integer('last_sale');
                }
            });
        }
    }
};
