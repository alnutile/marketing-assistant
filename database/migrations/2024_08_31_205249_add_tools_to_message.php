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
        Schema::table('messages', function (Blueprint $table) {
            $table->json('tool_args')->nullable();
            $table->string('tool_id')->nullable();
            $table->string('tool_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message', function (Blueprint $table) {
            $table->dropColumn([
                'tool_args',
                'tool_id',
                'tool_name',
            ]);
        });
    }
};
