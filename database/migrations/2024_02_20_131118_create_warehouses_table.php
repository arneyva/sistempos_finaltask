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
        Schema::create('warehouses', function (Blueprint $table) {
			$table->integer('id', true); //auto increment
			$table->string('name', 192)->comment('Nama gudang/outlet')->unique();
			$table->string('city', 192)->nullable()->comment('Nama Kota');
			$table->string('telephone', 192)->nullable()->comment('nomor handphone')->unique();
			$table->string('postcode', 192)->nullable()->comment('kode pos');
			$table->string('email', 192)->nullable()->comment('email')->unique();
			$table->string('country', 192)->nullable()->comment('nama negara');
            $table->integer('status')->default(1)->comment('1 = Active, 0 = NonActive');
			$table->timestamps(6);
			$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
