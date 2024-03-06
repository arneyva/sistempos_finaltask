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
        $tables1 = ['attendances', 'employees', 'holidays'];

        foreach ($tables1 as $table1) {
            if (Schema::hasTable($table1)) {
                Schema::table($table1, function (Blueprint $table) use ($table1) {
                    if (Schema::hasColumn($table1, 'company_id')) {
                        $table->dropForeign($table1.'_company_id');
                        $table->dropColumn('company_id');
                    }
                });
            }
        }

        $tables2 = ['departments', 'designations', 'office_shifts', 'leaves'];

        foreach ($tables2 as $table2) {
            if (Schema::hasTable($table2)) {
                Schema::table($table2, function (Blueprint $table) use ($table2) {
                    if (Schema::hasColumn($table2, 'company_id')) {
                        $constraintName = substr($table2, 0, -1).'_company_id';
                        $table->dropForeign($constraintName);
                        $table->dropColumn('company_id');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables1 = ['attendances', 'employees', 'holidays'];

        foreach ($tables1 as $table1) {
            if (Schema::hasTable($table1)) {
                Schema::table($table1, function (Blueprint $table) use ($table1) {
                    if (Schema::hasColumn($table1, 'company_id')) {
                        $table->foreign('company_id', $table1.'_company_id')->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                    }
                    if (! Schema::hasColumn($table1, 'company_id')) {
                        $table->integer('company_id')->index($table1.'_company_id');
                        $table->foreign('company_id', $table1.'_company_id')->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                    }
                });
            }
        }

        $tables2 = ['departments', 'designations', 'office_shifts', 'leaves'];

        foreach ($tables2 as $table2) {
            if (Schema::hasTable($table2)) {
                Schema::table($table2, function (Blueprint $table) use ($table2) {
                    if (Schema::hasColumn($table2, 'company_id')) {
                        $constraintName = substr($table2, 0, -1).'_company_id'; // Define the constraint name
                        $table->foreign('company_id', $constraintName)->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                    }
                    if (! Schema::hasColumn($table2, 'company_id')) {
                        $constraintName = substr($table2, 0, -1).'_company_id'; // Define the constraint name
                        $table->integer('company_id')->index($constraintName);
                        $table->foreign('company_id', $constraintName)->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                    }
                });
            }
        }
    }
};
