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
        Schema::table('sale_details', function (Blueprint $table) {
            $table->softDeletes(); // deleted_at 컬럼 추가
        });
    }

    public function down()
    {
        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropSoftDeletes(); // 롤백 시 deleted_at 컬럼 제거
        });
    }
};
