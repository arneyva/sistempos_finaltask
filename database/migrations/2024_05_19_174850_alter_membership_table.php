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
                if (! Schema::hasColumn($table->getTable(), 'one_score_equal')) {
                    $table->float('one_score_equal', 10, 0)->nullable(false)->default(1);
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
                if (Schema::hasColumn($table->getTable(), 'one_score_equal')) {
                    $table->dropColumn('one_score_equal');
                }
            });
        }
    }
};
