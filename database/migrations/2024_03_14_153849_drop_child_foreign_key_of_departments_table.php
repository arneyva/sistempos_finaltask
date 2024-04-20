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
        if (Schema::hasTable('leaves')) {
            Schema::table('leaves', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'department_id')) {
                    $table->dropForeign('leave_department_id');
                    $table->dropColumn('department_id');
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
                if (Schema::hasColumn($table->getTable(), 'department_id')) {
                    $table->foreign('department_id', 'leave_department_id')->references('id')->on('departments')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                } else {
                    $table->integer('department_id')->index('leave_department_id');
                    $table->foreign('department_id', 'leave_department_id')->references('id')->on('departments')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                }
            });
        }
    }
};
