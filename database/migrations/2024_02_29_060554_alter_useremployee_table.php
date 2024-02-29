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
            $table->dropColumn(['statut']);
            $table->string('country', 192)->after('phone')->nullable();
            $table->string('city', 192)->nullable()->after('country');
            $table->string('province', 192)->nullable()->after('city');
            $table->string('zipcode', 192)->nullable()->after('province');
            $table->string('address', 192)->nullable()->after('zipcode');
            $table->string('gender', 192)->after('address');
            $table->string('resume', 192)->nullable()->after('gender');
            $table->string('document', 192)->nullable()->after('resume');
            $table->date('birth_date')->nullable()->after('document');
            $table->date('joining_date')->nullable()->after('birth_date');
            $table->tinyInteger('remaining_leave')->nullable()->after('joining_date');
            $table->tinyInteger('total_leave')->nullable()->after('remaining_leave');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['country', 'city', 'province', 'zipcode', 'address', 'gender', 'resume', 'document', 'birth_date', 'joining_date', 'remaining_leave', 'total_leave']);
            $table->string('statut', 192)->nullable();
        });
    }
};
