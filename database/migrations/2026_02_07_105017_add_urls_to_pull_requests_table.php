<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('pull_requests', function (Blueprint $table) {
            // $table->string('diff_url')->nullable()->after('state');
            // $table->string('html_url')->nullable()->after('diff_url');
            //クローズ済みのリクエストかどうかの判断カラム。0209 1:39追加
            $table->boolean('is_closed')->default(false)->after('html_url');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pull_requests', function (Blueprint $table) {
            //
        });
    }
};
