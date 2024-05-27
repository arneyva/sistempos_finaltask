<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('providers', function (Blueprint $table) {
            // Menambahkan kolom baru
            $table->string('nama_kontak_person')->nullable();
            $table->string('alamat_website')->nullable();
            $table->integer('lead_time')->nullable();
            $table->string('nomor_kontak_person')->nullable();
            $table->string('avatar')->nullable();
        });
    }

    public function down()
    {
        Schema::table('providers', function (Blueprint $table) {
            // Menghapus kolom yang baru ditambahkan
            $table->dropColumn(['nama_kontak_person', 'alamat_website', 'lead_time', 'nomor_kontak_person', 'avatar']);
        });
    }
};
