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
        Schema::dropIfExists('designations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('designations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('company_id')->index('designation_company_id');
            $table->integer('department_id')->index('designation_departement_id');
            $table->string('designation', 192);
            $table->timestamps(6);
            $table->softDeletes();
        });
    }
};
