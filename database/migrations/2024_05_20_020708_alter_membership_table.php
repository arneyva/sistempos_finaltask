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
        if (Schema::hasTable('membership')) {
            Schema::table('membership', function (Blueprint $table) {
                if ( Schema::hasColumn($table->getTable(), 'one_score_equal')) {
                    $table->float('one_score_equal', 10, 0)->default(100)->change();
                }
            });
            Schema::table('membership', function (Blueprint $table) {
                if ( Schema::hasColumn($table->getTable(), 'spend_every')) {
                    $table->float('spend_every', 10, 0)->default(1000)->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('membership')) {
            Schema::table('membership', function (Blueprint $table) {
                if ( Schema::hasColumn($table->getTable(), 'one_score_equal')) {
                    $table->float('one_score_equal', 10, 0)->default(100)->change();
                }
            });
            Schema::table('membership', function (Blueprint $table) {
                if ( Schema::hasColumn($table->getTable(), 'spend_every')) {
                    $table->float('spend_every', 10, 0)->default(1000)->change();
                }
            });
        }
    }
};
