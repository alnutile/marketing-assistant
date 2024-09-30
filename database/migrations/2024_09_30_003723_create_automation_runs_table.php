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
        Schema::create('automation_runs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('completed_at')->nullable();
            $table->string('status')->default('pending');
            $table->foreignIdFor(\App\Models\Automation::class)->constrained();
            $table->longText('payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_runs');
    }
};
