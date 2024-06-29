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
        Schema::table('providers', function (Blueprint $table) {
            $table->string('payment_method')->nullable();
            $table->string('payment_term')->nullable();
            $table->string('courier')->nullable();
            $table->integer('down_payment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn(['payment_method','payment_term','courier','down_payment']);
        });
    }
};
