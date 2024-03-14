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
        if (Schema::hasTable('office_shift_user') && Schema::hasTable('office_shifts') && Schema::hasTable('users')) {
            Schema::table('office_shift_user', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'office_shift_id')) {
                    $table->foreign('office_shift_id', 'office_shift_user_office_shift_id')->references('id')->on('office_shifts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                }
                else {
                    $table->integer('office_shift_id')->index('office_shift_user_office_shift_id');
                    $table->foreign('office_shift_id', 'office_shift_user_office_shift_id')->references('id')->on('office_shifts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                }

                if (Schema::hasColumn($table->getTable(), 'user_id')) {
                    $table->foreign('user_id', 'office_shift_user_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                }
                else {
                    $table->integer('user_id')->index('office_shift_user_user_id');
                    $table->foreign('user_id', 'office_shift_user_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('office_shift_user')) {
            Schema::table('office_shift_user', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'office_shift_id')) {
                    $table->dropForeign('office_shift_user_office_shift_id');
                    $table->dropColumn('office_shift_id');
                }

                if (Schema::hasColumn($table->getTable(), 'user_id')) {
                    $table->dropForeign('office_shift_user_user_id');
                    $table->dropColumn('user_id');
                }
            });
        }
    }
};
