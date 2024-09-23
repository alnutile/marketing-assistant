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
        Schema::rename('campaigns', 'projects');

        Schema::rename('campaign_user', 'project_user');

        Schema::table('messages', function (Blueprint $table) {
            $table->renameColumn('campaign_id', 'project_id');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->renameColumn('campaign_id', 'project_id');
        });

        Schema::table('project_user', function (Blueprint $table) {
            $table->renameColumn('campaign_id', 'project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
