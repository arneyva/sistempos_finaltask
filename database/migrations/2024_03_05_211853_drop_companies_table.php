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
    }
};
