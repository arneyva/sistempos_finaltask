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
        

        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'designation_id')) {
                    $table->dropForeign('employees_designation_id');
                    $table->dropColumn('designation_id');
                }
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'designation_id')) {
                    $table->foreign('designation_id', 'employees_designation_id')->references('id')->on('employees')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                }
                if (!Schema::hasColumn($table->getTable(), 'designation_id')) {
                    $table->integer('designation_id')->index('employees_designation_id');
                    $table->foreign('designation_id', 'employees_designation_id')->references('id')->on('employees')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                }
            });
        }

    }
};
