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
        Schema::table('purchase_returns', function (Blueprint $table) {
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
            $table->string('request_address')->nullable();
            $table->date('request_req_arrive_date')->nullable();
            $table->string('request_courier')->nullable();
            $table->string('request_shipment_number')->nullable();
            $table->string('request_driver_contact')->nullable();
            $table->float('request_shipment_cost')->nullable();
            $table->float('subtotal')->nullable();
            $table->string('request_estimate_arrive_date')->nullable();
            $table->string('request_delivery_file')->nullable();
            $table->string('address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'payment_term',
                'courier',
                'down_payment',
                'supplier_notes',
                'req_arrive_date',
                'estimate_arrive_date',
                'shipment_number',
                'shipment_cost',
                'driver_contact',
                'barcode',
                'address',
                'subtotal',
                'delivery_file',
                'request_address',
                'request_req_arrive_date',
                'request_courier',
                'request_shipment_number',
                'request_driver_contact',
                'request_shipment_cost',
                'request_estimate_arrive_date',
                'request_delivery_file'
            ]);
        });
    }
};
