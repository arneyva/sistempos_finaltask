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
        Schema::table('attendances', function (Blueprint $table) {
            $table->date('date')->nullable()->change();
			$table->string('clock_in', 191)->nullable()->change();
			$table->string('clock_in_ip', 45)->nullable()->change();
			$table->string('clock_out', 191)->nullable()->change();
			$table->string('clock_out_ip', 191)->nullable()->change();
			$table->boolean('clock_in_out')->nullable()->change();
			$table->string('depart_early', 191)->nullable()->change();
			$table->string('late_time', 191)->nullable()->change();
			$table->string('overtime', 191)->nullable()->change();
			$table->string('total_work', 191)->nullable()->change();
			$table->string('total_rest', 191)->nullable()->change();
			$table->string('status', 191)->nullable()->change();
			$table->string('late_in', 191)->nullable();
			$table->string('late_out', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->date('date')->change();
			$table->string('clock_in', 191)->change();
			$table->string('clock_in_ip', 45)->change();
			$table->string('clock_out', 191)->change();
			$table->string('clock_out_ip', 191)->change();
			$table->boolean('clock_in_out')->change();
			$table->string('depart_early', 191)->change();
			$table->string('late_time', 191)->change();
			$table->string('overtime', 191)->change();
			$table->string('total_work', 191)->change();
			$table->string('total_rest', 191)->change();
			$table->string('status', 191)->change();
            $table->dropColumn(['late_in', 'late_out']);
        });
    }
};
