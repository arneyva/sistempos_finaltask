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
        if (Schema::hasTable('office_shift_warehouse') && Schema::hasTable('office_shifts') && Schema::hasTable('warehouses')) {
            Schema::table('office_shift_warehouse', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'office_shift_id')) {
                    $table->foreign('office_shift_id', 'office_shift_warehouse_office_shift_id')->references('id')->on('office_shifts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                } else {
                    $table->integer('office_shift_id')->index('office_shift_warehouse_office_shift_id');
                    $table->foreign('office_shift_id', 'office_shift_warehouse_office_shift_id')->references('id')->on('office_shifts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                }

                if (Schema::hasColumn($table->getTable(), 'warehouse_id')) {
                    $table->foreign('warehouse_id', 'office_shift_warehouse_warehouse_id')->references('id')->on('warehouses')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                } else {
                    $table->integer('warehouse_id')->index('office_shift_warehouse_warehouse_id');
                    $table->foreign('warehouse_id', 'office_shift_warehouse_warehouse_id')->references('id')->on('warehouses')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('office_shift_warehouse')) {
            Schema::table('office_shift_warehouse', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'office_shift_id')) {
                    $table->dropForeign('office_shift_warehouse_office_shift_id');
                    $table->dropColumn('office_shift_id');
                }

                if (Schema::hasColumn($table->getTable(), 'warehouse_id')) {
                    $table->dropForeign('office_shift_warehouse_warehouse_id');
                    $table->dropColumn('warehouse_id');
                }
            });
        }
    }
};
