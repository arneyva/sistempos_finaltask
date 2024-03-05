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
        Schema::table('office_shifts', function (Blueprint $table) {
            if (Schema::hasColumn('office_shifts', 'company_id')) {
                $table->dropForeign('office_shift_company_id');
                
            }
        });

        Schema::table('leaves', function (Blueprint $table) {
            if (Schema::hasColumn('leaves', 'company_id')) {
                $table->dropForeign('leave_company_id');
                
            }
        });

        Schema::table('holidays', function (Blueprint $table) {
            if (Schema::hasColumn('holidays', 'company_id')) {
                $table->dropForeign('holidays_company_id');
                
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'company_id')) {
                $table->dropForeign('employees_company_id');
                
            }
        });

        Schema::table('designations', function (Blueprint $table) {
            if (Schema::hasColumn('designations', 'company_id')) {
                $table->dropForeign('designation_company_id');
                
            }
        });

        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'company_id')) {
                $table->dropForeign('attendances_company_id');
                
            }
        });

        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'company_id')) {
                $table->dropForeign('department_company_id');
                
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            Schema::table('office_shifts', function (Blueprint $table) {
                if (Schema::hasColumn('office_shifts', 'company_id')) {
                    $table->foreign('office_shift_company_id');
                    
                }
            });

            Schema::table('leaves', function (Blueprint $table) {
                if (Schema::hasColumn('leaves', 'company_id')) {
                    $table->foreign('leave_company_id');
                    
                }
            });

            Schema::table('holidays', function (Blueprint $table) {
                if (Schema::hasColumn('holidays', 'company_id')) {
                    $table->foreign('holidays_company_id');
                    
                }
            });

            Schema::table('employees', function (Blueprint $table) {
                if (Schema::hasColumn('employees', 'company_id')) {
                    $table->foreign('employees_company_id');
                    
                }
            });

            Schema::table('designations', function (Blueprint $table) {
                if (Schema::hasColumn('designations', 'company_id')) {
                    $table->foreign('designation_company_id');
                    
                }
            });

            Schema::table('attendances', function (Blueprint $table) {
                if (Schema::hasColumn('attendances', 'company_id')) {
                    $table->foreign('attendances_company_id');
                    
                }
            });

            Schema::table('departments', function (Blueprint $table) {
                if (Schema::hasColumn('departments', 'company_id')) {
                    $table->foreign('department_company_id');
                    
                }
            });
    }
};
