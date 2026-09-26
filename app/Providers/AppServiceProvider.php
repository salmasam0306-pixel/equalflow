<?php

namespace App\Providers;

use App\Models\Project;
use App\Observers\ProjectObserver;
use App\Services\AIService;
use App\Services\NotificationService;
use App\Services\WorkloadAnalyzer;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AIService::class, function ($app) {
            return new AIService();
        });

        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService();
        });

        $this->app->singleton(WorkloadAnalyzer::class, function ($app) {
            return new WorkloadAnalyzer();
        });
    }

    public function boot(): void
    {
        Project::observe(ProjectObserver::class);
    }
}