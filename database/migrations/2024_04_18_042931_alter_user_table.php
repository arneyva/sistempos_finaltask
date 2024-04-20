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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'gender')) {
                    DB::table('users')->delete();
                    $table->dropColumn('gender');
                }
            });
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn($table->getTable(), 'gender')) {
                    DB::table('users')->delete();
                    $table->enum('gender', ['Laki-laki', 'Perempuan']);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'gender')) {
                    DB::table('users')->delete();
                    $table->dropColumn('gender');
                }
            });
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn($table->getTable(), 'gender')) {
                    DB::table('users')->delete();
                    $table->string('gender');
                }
            });
        }
    }
};
