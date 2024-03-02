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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('hourly_rate', 10, 2)->nullable()->after('total_leave');
            $table->decimal('basic_salary', 10, 2)->nullable()->after('hourly_rate');
            $table->string('employment_type', 192)->nullable()->after('basic_salary');
            $table->string('marital_status', 192)->nullable()->after('employment_type');
            $table->string('facebook', 192)->nullable()->after('marital_status');
            $table->string('skype', 192)->nullable()->after('facebook');
            $table->string('whatsapp', 192)->nullable()->after('skype');
            $table->string('twitter', 192)->nullable()->after('whatsapp');
            $table->string('linkedin', 192)->nullable()->after('twitter');
            $table->date('leaving_date')->nullable()->after('linkedin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['hourly_rate', 'basic_salary', 'employment_type', 'marital_status', 'facebook', 'skype', 'whatsapp', 'twitter', 'linkedin', 'leaving_date']);
        });
    }
};
