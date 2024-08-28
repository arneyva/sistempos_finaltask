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
        Schema::table('payment_purchases', function (Blueprint $table) {
            $table->string('payment_proof')->nullable();
        });

        if (Schema::hasTable('payment_purchases') && Schema::hasTable('purchase_returns')) {
            Schema::table('payment_purchases', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'purchase_return_id')) {
                    $table->foreign('purchase_return_id', 'purchase_returns_purchase_return_id')->references('id')->on('purchase_returns')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                } else {
                    $table->integer('purchase_return_id')->index('purchase_returns_purchase_return_id')->nullable();
                    $table->foreign('purchase_return_id', 'purchase_returns_purchase_return_id')->references('id')->on('purchase_returns')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_purchases', function (Blueprint $table) {
            $table->dropColumn(['payment_proof']);
        });

        if (Schema::hasTable('payment_purchases')) {
            Schema::table('payment_purchases', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'purchase_return_id')) {
                    $table->dropForeign('purchase_returns_purchase_return_id');
                    $table->dropColumn('purchase_return_id');
                }
            });
        }
    }
};
