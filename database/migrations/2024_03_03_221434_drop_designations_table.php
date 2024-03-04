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
        
        //drop all foreign child of designations
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign('employees_designation_id');
            $table->dropColumn('designation_id');
        });
        //
        // drop designations table
        Schema::dropIfExists('designations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
