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
        if (Schema::hasTable('attendances') && Schema::hasTable('users')) {
            Schema::table('attendances', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'admin_id')) {
                    $table->foreign('admin_id', 'attendance_admin_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                } else {
                    $table->integer('admin_id')->index('attendance_admin_id')->nullable();
                    $table->foreign('admin_id', 'attendance_admin_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('attendances')) {
            Schema::table('attendances', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'admin_id')) {
                    $table->dropForeign('attendance_admin_id');
                    $table->dropColumn('admin_id');
                }
            });
        }
    }
};
