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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('details');
            $table->boolean('completed')->default(false);
            $table->foreignIdFor(\App\Models\Campaign::class)->constrained();
            $table->foreignIdFor(\App\Models\User::class)->constrained()->nullable();
            $table->date('due_date')->nullable();
            $table->boolean('assistant')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
