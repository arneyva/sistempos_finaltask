<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('payment_purchases', function (Blueprint $table) {

            // Change the column to be nullable
            $table->integer('user_id')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_purchases', function (Blueprint $table) {
            // Drop the existing foreign key constraint

            // Revert the column to be non-nullable
            $table->integer('user_id')->nullable(false)->change();

        });
    }
};
