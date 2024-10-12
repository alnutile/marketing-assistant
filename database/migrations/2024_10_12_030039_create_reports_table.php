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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Project::class)->constrained();
            $table->string('report_type')->default(\App\Domains\Reports\ReportTypes::StandardsChecking->value);
            $table->string('status')->nullable();
            $table->string('summary_of_results')->nullable();
            $table->string('file_name')->nullable();
            $table->longText('prompt')->nullable();
            $table->integer('overall_score')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
