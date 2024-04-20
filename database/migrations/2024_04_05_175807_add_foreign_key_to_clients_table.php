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
        if (Schema::hasTable('clients') && Schema::hasTable('client_tiers')) {
            Schema::table('clients', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'client_tier_id')) {
                    $table->foreign('client_tier_id', 'clients_client_tier_id')->references('id')->on('client_tiers')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                } else {
                    DB::table('clients')->delete();
                    $table->integer('client_tier_id')->index('clients_client_tier_id');
                    $table->foreign('client_tier_id', 'clients_client_tier_id')->references('id')->on('client_tiers')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('clients')) {
            Schema::table('clients', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'client_tier_id')) {
                    DB::table('clients')->delete();
                    $table->dropForeign('clients_client_tier_id');
                    $table->dropColumn('client_tier_id');
                }
            });
        }
    }
};
