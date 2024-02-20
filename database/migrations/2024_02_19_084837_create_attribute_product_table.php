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
        Schema::create('brands', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 192);
            $table->string('description', 192)->nullable();
            $table->string('image', 192)->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('code', 192)->unique();
            $table->string('name', 192);
            $table->timestamps(6);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
    }
};
