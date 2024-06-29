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
        Schema::table('purchases', function (Blueprint $table) {
            $table->string('payment_method')->nullable();
            $table->string('payment_term')->nullable();
            $table->string('courier')->nullable();
            $table->integer('down_payment')->nullable();
            $table->text('supplier_notes')->nullable();
            $table->date('req_arrive_date')->nullable();
            $table->date('estimate_arrive_date')->nullable();
            $table->string('shipment_number')->nullable();
            $table->float('shipment_cost')->nullable();
            $table->string('driver_contact')->nullable();
            $table->string('barcode')->nullable();
            $table->string('delivery_file')->nullable();
        });

        if (Schema::hasTable('purchases') && Schema::hasTable('users')) {
            Schema::table('purchases', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'checked_by')) {
                    $table->foreign('checked_by', 'purchase_checked_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                } else {
                    $table->integer('checked_by')->index('purchase_checked_by')->nullable();
                    $table->foreign('checked_by', 'purchase_checked_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['payment_method','payment_term','courier','down_payment','supplier_notes','req_arrive_date','estimate_arrive_date','shipment_number','shipment_cost','driver_contact','barcode','delivery_file']);
        });

        if (Schema::hasTable('purchases')) {
            Schema::table('purchases', function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'checked_by')) {
                    $table->dropForeign('purchase_checked_by');
                    $table->dropColumn('checked_by');
                }
            });
        }
    }
};
