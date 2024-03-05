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
            if (Schema::hasColumn('employee_accounts', 'employee_id')) {
                $table->dropForeign('employee_accounts_employee_id');
                
            }
        });

        Schema::table('employee_experiences', function (Blueprint $table) {
            if (Schema::hasColumn('employee_experiences', 'employee_id')) {
                $table->dropForeign('employee_experience_employee_id');
                
            }
        });

        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'employee_id')) {
                $table->dropForeign('attendances_employee_id');
                
            }
        });

        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'employee_id')) {
                $table->dropForeign('department_department_head');
                
            }
        });

        Schema::table('leaves', function (Blueprint $table) {
            if (Schema::hasColumn('leaves', 'employee_id')) {
                $table->dropForeign('leave_employee_id');
                
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
                    Schema::table('employee_accounts', function (Blueprint $table) {
                if (Schema::hasColumn('employee_accounts', 'employee_id')) {
                    $table->foreign('employee_accounts_employee_id');
                    
                }
            });

            Schema::table('employee_experiences', function (Blueprint $table) {
                if (Schema::hasColumn('employee_experiences', 'employee_id')) {
                    $table->foreign('employee_experience_employee_id');
                    
                }
            });

            Schema::table('attendances', function (Blueprint $table) {
                if (Schema::hasColumn('attendances', 'employee_id')) {
                    $table->foreign('attendances_employee_id');
                    
                }
            });

            Schema::table('departments', function (Blueprint $table) {
                if (Schema::hasColumn('departments', 'employee_id')) {
                    $table->foreign('department_department_head');
                    
                }
            });

            Schema::table('leaves', function (Blueprint $table) {
                if (Schema::hasColumn('leaves', 'employee_id')) {
                    $table->foreign('leave_employee_id');
                    
                }
            });
    }
};
