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
        if (Schema::hasTable('clients')) {
            Schema::table('clients', function (Blueprint $table) {
                if ( Schema::hasColumn($table->getTable(), 'is_poin_activated')) {
                    $table->dropColumn('is_poin_activated');
                }
            });
            Schema::table('clients', function (Blueprint $table) {
                if (! Schema::hasColumn($table->getTable(), 'is_poin_activated')) {
                    $table->boolean('is_poin_activated')->default(0);
                }
            });
            Schema::table('clients', function (Blueprint $table) {
                if ( Schema::hasColumn($table->getTable(), 'score')) {
                    $table->float('score', 10, 0)->nullable(false)->default(0)->change();
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
                if (!Schema::hasColumn($table->getTable(), 'is_poin_activated')) {
                    $table->boolean('is_poin_activated')->default(0);
                }
            });
            Schema::table('clients', function (Blueprint $table) {
                if ( Schema::hasColumn($table->getTable(), 'score')) {
                    $table->float('score', 10, 0)->nullable()->default(null)->change();
                }
            });
        }
    }
};
