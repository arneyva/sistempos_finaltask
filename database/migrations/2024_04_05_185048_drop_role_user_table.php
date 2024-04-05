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
        Schema::dropIfExists('role_user');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('user_id')->index('role_user_user_id');
            $table->integer('role_id')->index('role_user_role_id');
            $table->timestamps(6);
        });
    }
};
