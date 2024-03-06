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
        $tables1 = ['attendances', 'employee_accounts'];

        foreach ($tables1 as $table1) {
            if (Schema::hasTable($table1)) {
                Schema::table($table1, function (Blueprint $table) use ($table1) {
                    if (Schema::hasColumn($table1, 'employee_id')) {
                        $table->dropForeign($table1.'_employee_id');
                        $table->dropColumn('employee_id');
                    }
                });
            }
        }

        if (Schema::hasTable('departments')) {
            Schema::table('departments', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'department_head')) {
                    $table->dropForeign('department_department_head');
                    $table->dropColumn('department_head');
                }
            });
        }

        $tables2 = ['employee_experiences', 'leaves'];

        foreach ($tables2 as $table2) {
            if (Schema::hasTable($table2)) {
                Schema::table($table2, function (Blueprint $table) use ($table2) {
                    if (Schema::hasColumn($table2, 'employee_id')) {
                        $constraintName = substr($table2, 0, -1).'_employee_id';
                        $table->dropForeign($constraintName);
                        $table->dropColumn('employee_id');
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
        $tables1 = ['attendances', 'employee_accounts'];

        foreach ($tables1 as $table1) {
            if (Schema::hasTable($table1)) {
                Schema::table($table1, function (Blueprint $table) use ($table1) {
                    if (Schema::hasColumn($table1, 'employee_id')) {
                        $table->foreign('employee_id', $table1.'_employee_id')->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                    }
                    if (! Schema::hasColumn($table1, 'employee_id')) {
                        $table->integer('employee_id')->index($table1.'_employee_id');
                        $table->foreign('employee_id', $table1.'_employee_id')->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                    }
                });
            }
        }

        if (Schema::hasTable('departments')) {
            Schema::table('departments', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'department_head')) {
                    $table->foreign('department_head', 'department_department_head')->references('id')->on('departments')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                }
                if (! Schema::hasColumn($table->getTable(), 'department_head')) {
                    $table->integer('department_head')->index('department_department_head');
                    $table->foreign('department_head', 'department_department_head')->references('id')->on('departments')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                }
            });
        }

        $tables2 = ['employee_experiences', 'leaves'];

        foreach ($tables2 as $table2) {
            if (Schema::hasTable($table2)) {
                Schema::table($table2, function (Blueprint $table) use ($table2) {
                    if (Schema::hasColumn($table2, 'employee_id')) {
                        $constraintName = substr($table2, 0, -1).'_employee_id'; // Define the constraint name
                        $table->foreign('employee_id', $constraintName)->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                    }
                    if (! Schema::hasColumn($table2, 'employee_id')) {
                        $constraintName = substr($table2, 0, -1).'_employee_id'; // Define the constraint name
                        $table->integer('employee_id')->index($constraintName);
                        $table->foreign('employee_id', $constraintName)->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                    }
                });
            }
        }
    }
};
