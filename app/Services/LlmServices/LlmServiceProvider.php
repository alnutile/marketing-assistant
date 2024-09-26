<?php

namespace App\Services\LlmServices;

use App\Services\LlmServices\Functions\CreateTasksTool;
use App\Services\LlmServices\Functions\SendEmailToTeam;
use App\Services\LlmServices\Functions\TaskList;
use Illuminate\Support\ServiceProvider;

class LlmServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind('llm_driver', function () {
            return new LlmDriverClient;
        });

        $this->app->bind('create_tasks_tool', function () {
            return new CreateTasksTool;
        });

        $this->app->bind('send_email_to_team', function () {
            return new SendEmailToTeam;
        });

        $this->app->bind('list_tasks', function () {
            return new TaskList();
        });
    }
}
