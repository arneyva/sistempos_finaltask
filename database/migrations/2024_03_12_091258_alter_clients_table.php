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
        if (Schema::hasTable('clients')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn(['adresse', 'tax_number', 'city', 'country']);
                $table->string('name', 255)->nullable()->change();
                $table->string('phone', 255)->change();
                $table->string('code', 11)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('clients')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->string('adresse', 255);
                $table->string('tax_number', 255);
                $table->string('city', 255);
                $table->string('country', 255);
                $table->string('name', 255)->change();
                $table->string('phone', 255)->nullable()->change();
                $table->integer('code')->change();
            });
        }
    }
};
