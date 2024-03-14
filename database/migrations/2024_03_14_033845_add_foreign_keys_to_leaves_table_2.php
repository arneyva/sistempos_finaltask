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
        if (Schema::hasTable('leaves') && Schema::hasTable('users') ) {
            Schema::table('leaves', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'user_id')) {
                    $table->foreign('user_id', 'leave_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                }
                else {
                    $table->integer('user_id')->index('leave_user_id');
                    $table->foreign('user_id', 'leave_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('leaves')) {
            Schema::table('leaves', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'user_id')) {
                    $table->dropForeign('leave_user_id');
                    $table->dropColumn('user_id');
                }
            });
        }
    }
};
