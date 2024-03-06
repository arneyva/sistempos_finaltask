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
            // // $table->dropColumn('company_id');
        });
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign('attendances_company_id');
            // $table->dropColumn('company_id');
        });
        Schema::table('designations', function (Blueprint $table) {
            $table->dropForeign('designation_company_id');
            // $table->dropColumn('company_id');
        });
        // Schema::table('employees', function (Blueprint $table) {
        //     $table->dropForeign('employees_company_id');
        //     // $table->dropColumn('company_id');
        // });
        Schema::table('holidays', function (Blueprint $table) {
            $table->dropForeign('holidays_company_id');
            // $table->dropColumn('company_id');
        });
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign('leave_company_id');
            // $table->dropColumn('company_id');
        });
        Schema::table('office_shifts', function (Blueprint $table) {
            $table->dropForeign('office_shift_company_id');
            // $table->dropColumn('company_id');
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
        Schema::create('companies', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('name', 191);
            $table->string('email', 191)->nullable();
            $table->string('phone', 191)->nullable();
            $table->string('country', 191)->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });

        Schema::table('departments', function (Blueprint $table) {
            // $table->integer('company_id')->index('department_company_id');
            $table->foreign('company_id', 'department_company_id')->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });

        Schema::table('attendances', function (Blueprint $table) {
            // $table->integer('company_id')->index('attendances_company_id');
            $table->foreign('company_id', 'attendances_company_id')->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });

        Schema::table('designations', function (Blueprint $table) {
            $table->foreign('company_id', 'designation_company_id')->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });

        // Schema::table('employees', function (Blueprint $table) {
        //     $table->foreign('company_id', 'employees_company_id')->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        // });

        Schema::table('holidays', function (Blueprint $table) {
            // $table->integer('company_id')->index('holidays_company_id');
            $table->foreign('company_id', 'holidays_company_id')->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });

        Schema::table('leaves', function (Blueprint $table) {
            // $table->integer('company_id')->index('leave_company_id');
            $table->foreign('company_id', 'leave_company_id')->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });

        Schema::table('office_shifts', function (Blueprint $table) {
            // $table->integer('company_id')->index('office_shift_company_id');
            $table->foreign('company_id', 'office_shift_company_id')->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }
};
