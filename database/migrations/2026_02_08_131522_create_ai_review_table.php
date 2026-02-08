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
        Schema::create('ai_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pull_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('coding_convention_id')->constrained();
            $table->longText('review_result')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
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
