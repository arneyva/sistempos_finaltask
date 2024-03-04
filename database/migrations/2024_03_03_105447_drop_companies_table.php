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
        //drop all foreign child of companies
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign('department_company_id');
            $table->dropColumn('company_id');
        });
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign('attendances_company_id');
            $table->dropColumn('company_id');
        });
        Schema::table('designations', function (Blueprint $table) {
            $table->dropForeign('designation_company_id');
            $table->dropColumn('company_id');
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign('employees_company_id');
            $table->dropColumn('company_id');
        });
        Schema::table('holidays', function (Blueprint $table) {
            $table->dropForeign('holidays_company_id');
            $table->dropColumn('company_id');
        });
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign('leave_company_id');
            $table->dropColumn('company_id');
        });
        Schema::table('office_shifts', function (Blueprint $table) {
            $table->dropForeign('office_shift_company_id');
            $table->dropColumn('company_id');
        });
        //
        // drop companies table
        Schema::dropIfExists('companies');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
