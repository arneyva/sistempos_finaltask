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
        Schema::dropIfExists('client_tiers');
        Schema::dropIfExists('email_messages');
        Schema::dropIfExists('employee_accounts');
        Schema::dropIfExists('employee_experiences');
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('leave_types');
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('payment_with_credit_card');
        Schema::dropIfExists('pos_settings');
        Schema::dropIfExists('quotation_details');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('servers');
        Schema::dropIfExists('sms_gateway');
        Schema::dropIfExists('sms_messages');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('client_tiers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('tier', 191);
            $table->integer('total_sales');
            $table->float('total_amount', 10, 0);
            $table->integer('last_sale');
            $table->float('discount', 10, 0);
            $table->float('score', 10, 0);
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('email_messages', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->text('name')->nullable();
            $table->text('subject')->nullable();
            $table->text('body')->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('employee_accounts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('employee_id')->index('employee_accounts_employee_id');
            $table->string('bank_name', 192);
            $table->string('bank_branch', 192);
            $table->string('account_no', 192);
            $table->text('note')->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('employee_experiences', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('employee_id')->index('employee_experience_employee_id');
            $table->string('title', 192);
            $table->string('company_name', 192);
            $table->string('location', 192)->nullable();
            $table->string('employment_type', 192);
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('holidays', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('title', 192);
            $table->integer('company_id')->index('holidays_company_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('leave_type', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('title', 192);
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('leaves', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('employee_id')->index('leave_employee_id');
            $table->integer('company_id')->index('leave_company_id');
            $table->integer('department_id')->index('leave_department_id');
            $table->integer('leave_type_id')->index('leave_leave_type_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('days', 192);
            $table->text('reason')->nullable();
            $table->string('attachment', 192)->nullable();
            $table->boolean('half_day')->nullable();
            $table->string('status', 192);
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('payment_with_credit_card', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('payment_id');
            $table->integer('customer_id');
            $table->string('customer_stripe_id', 192);
            $table->string('charge_id', 192);
            $table->timestamps(6);
        });
        Schema::create('pos_settings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('note_customer', 192)->default('Thank You For Shopping With Us . Please Come Again');
            $table->boolean('show_note')->default(1);
            $table->boolean('show_barcode')->default(1);
            $table->boolean('show_discount')->default(1);
            $table->boolean('show_customer')->default(1);
            $table->boolean('show_email')->default(1);
            $table->boolean('show_phone')->default(1);
            $table->boolean('show_address')->default(1);
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('quotations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('user_id')->index('user_id_quotation');
            $table->date('date');
            $table->string('Ref', 192);
            $table->integer('client_id')->index('client_id_quotation');
            $table->integer('warehouse_id')->index('warehouse_id_quotation');
            $table->float('tax_rate', 10, 0)->nullable()->default(0);
            $table->float('TaxNet', 10, 0)->nullable()->default(0);
            $table->float('discount', 10, 0)->nullable()->default(0);
            $table->float('shipping', 10, 0)->nullable()->default(0);
            $table->float('GrandTotal', 10, 0);
            $table->string('statut', 192);
            $table->text('notes')->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('quotation_details', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->float('price', 10, 0);
            $table->float('TaxNet', 10, 0)->nullable()->default(0);
            $table->string('tax_method', 192)->nullable()->default('1');
            $table->float('discount', 10, 0)->nullable()->default(0);
            $table->string('discount_method', 192)->nullable()->default('1');
            $table->float('total', 10, 0);
            $table->float('quantity', 10, 0);
            $table->integer('product_id')->index('product_id_quotation_details');
            $table->integer('product_variant_id')->nullable()->index('quote_product_variant_id');
            $table->integer('quotation_id')->index('quotation_id');
            $table->timestamps(6);
        });
        Schema::create('servers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('host', 191);
            $table->integer('port');
            $table->string('username', 191);
            $table->string('password', 191);
            $table->string('encryption', 191);
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('sms_gateway', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('title', 192);
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->text('name')->nullable();
            $table->text('text')->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });
    }
};
