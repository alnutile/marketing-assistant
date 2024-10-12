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
        Schema::create('report_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Report::class)->constrained();
            $table->longText('review')->nullable();
            $table->integer('sort')->default(0);
            $table->integer('score')->default(0);
            $table->longText('content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_pages');
    }
};
