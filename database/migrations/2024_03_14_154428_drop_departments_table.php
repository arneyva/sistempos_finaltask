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
        Schema::dropIfExists('departments');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('department', 191);
            $table->integer('company_id')->index('department_company_id');
            $table->integer('department_head')->nullable()->index('department_department_head');
            $table->timestamps(6);
            $table->softDeletes();
        });
    }
};
