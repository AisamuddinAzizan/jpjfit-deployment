<?php

namespace App\Providers;

use App\Services\MailSettingsService;
use App\Support\WindowsSafeFilesystem;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return;
        }

        $this->app->singleton('files', static fn (): WindowsSafeFilesystem => new WindowsSafeFilesystem());
        $this->app->alias('files', Filesystem::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app(MailSettingsService::class)->applyRuntimeConfig();
    }
}
