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
        Schema::dropIfExists('permission_role');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('permission_role', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('permission_id')->index('permission_role_permission_id');
            $table->integer('role_id')->index('permission_role_role_id');
        });
    }
};
