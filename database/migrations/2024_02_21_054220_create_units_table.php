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
        Schema::create('units', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 192);
            $table->string('short_name', 192);
            $table->integer('base_unit_id')->nullable();
            $table->char('operator', 192)->nullable()->default('*');
            $table->float('operator_value', 10, 0)->nullable()->default(1);
            $table->string('description')->nullable();
            $table->integer('is_active')->default(1);
            $table->timestamps(6);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
