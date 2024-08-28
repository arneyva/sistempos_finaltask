<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_attendances', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('file_pendukung')->nullable();
            $table->string('details')->nullable();
            $table->date('agreed_at')->nullable();
            $table->date('date')->nullable();
            $table->integer('status')->nullable();
            $table->integer('user_id')->index('user_requested_id')->nullable();
            $table->foreign('user_id', 'user_requested_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->integer('admin_id')->index('user_agreed_id')->nullable();
            $table->foreign('admin_id', 'user_agreed_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->integer('attendance_id')->index('attendance_requested_id')->nullable();
            $table->foreign('attendance_id', 'attendance_requested_id')->references('id')->on('attendances')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->timestamps(6);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('request_attendances');
    }
};
