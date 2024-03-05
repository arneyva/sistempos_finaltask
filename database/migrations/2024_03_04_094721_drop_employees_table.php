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
        //drop all foreign child of employees
        Schema::table('employee_accounts', function (Blueprint $table) {
            $table->dropForeign('employee_accounts_employee_id');
            // $table->dropColumn('employee_id');
        });

        Schema::table('employee_experiences', function (Blueprint $table) {
            $table->dropForeign('employee_experience_employee_id');
            // $table->dropColumn('employee_id');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign('attendances_employee_id');
            // $table->dropColumn('employee_id');
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign('leave_employee_id');
            // $table->dropColumn('employee_id');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign('department_department_head');
            // $table->dropColumn('department_head');
        });
        
        //
        // drop designations table
        Schema::dropIfExists('employees');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('firstname', 192);
            $table->string('lastname', 192);
            $table->string('username', 191);
            $table->string('email', 192)->nullable();
            $table->string('phone', 192)->nullable();
            $table->string('country', 192)->nullable();
            $table->string('city', 192)->nullable();
            $table->string('province', 192)->nullable();
            $table->string('zipcode', 192)->nullable();
            $table->string('address', 192)->nullable();
            $table->string('gender', 192);
            $table->string('resume', 192)->nullable();
            $table->string('avatar', 192)->nullable()->default('no_avatar.png');
            $table->string('document', 192)->nullable();
            $table->date('birth_date')->nullable();
            $table->date('joining_date')->nullable();
            $table->integer('company_id')->index('employees_company_id');
            $table->integer('department_id')->index('employees_department_id');
            $table->integer('designation_id')->index('employees_designation_id');
            $table->integer('office_shift_id')->index('employees_office_shift_id');
            $table->boolean('remaining_leave')->nullable()->default(0);
            $table->boolean('total_leave')->nullable()->default(0);
            $table->decimal('hourly_rate', 10)->nullable()->default(0.00);
            $table->decimal('basic_salary', 10)->nullable()->default(0.00);
            $table->string('employment_type', 192)->nullable()->default('full_time');
            $table->date('leaving_date')->nullable();
            $table->string('marital_status', 192)->nullable()->default('single');
            $table->string('facebook', 192)->nullable();
            $table->string('skype', 192)->nullable();
            $table->string('whatsapp', 192)->nullable();
            $table->string('twitter', 192)->nullable();
            $table->string('linkedin', 192)->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });

        Schema::table('employee_accounts', function (Blueprint $table) {
            // $table->integer('employee_id')->index('employee_accounts_employee_id');
            $table->foreign('employee_id', 'employee_accounts_employee_id')->references('id')->on('employees')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
        
        Schema::table('employee_experiences', function (Blueprint $table) {
            // $table->integer('employee_id')->index('employee_experience_employee_id');
            $table->foreign('employee_id', 'employee_experience_employee_id')->references('id')->on('employees')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
        
        Schema::table('attendances', function (Blueprint $table) {
            // $table->integer('employee_id')->index('attendances_employee_id');
            $table->foreign('employee_id', 'attendances_employee_id')->references('id')->on('employees')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
        
        Schema::table('leaves', function (Blueprint $table) {
            // $table->integer('employee_id')->index('leave_employee_id');
            $table->foreign('employee_id', 'leave_employee_id')->references('id')->on('employees')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
        
        Schema::table('departments', function (Blueprint $table) {
            // $table->integer('department_head')->index('department_department_head');
            $table->foreign('department_head', 'department_department_head')->references('id')->on('employees')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }
};
