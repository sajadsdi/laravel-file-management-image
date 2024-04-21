<?php

namespace Sajadsdi\LaravelFileManagementImage\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Sajadsdi\LaravelFileManagement\Events\AfterUpload;
use Sajadsdi\LaravelFileManagement\Events\BeforeUpload;
use Sajadsdi\LaravelFileManagementImage\Console\PublishCommand;
use Sajadsdi\LaravelFileManagementImage\Listeners\AfterUploadListener;
use Sajadsdi\LaravelFileManagementImage\Listeners\BeforeUploadListener;
use Sajadsdi\LaravelFileManagementImage\Services\ImageService;

class FileManagementImageServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $config = config('file-management-image');

        $this->app->singleton(ImageService::class, function () use ($config) {
            return new ImageService($config['process_driver']);
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->configurePublishing();
            $this->registerCommands();
        }

        $this->setListeners();
    }

    public function setListeners()
    {
        Event::listen(
            BeforeUpload::class,
            BeforeUploadListener::class
        );

        Event::listen(
            AfterUpload::class,
            AfterUploadListener::class
        );
    }

    private function configurePublishing()
    {
        $this->publishes([__DIR__ . '/../../config/file-management-image.php' => config_path('file-management-image.php')], 'laravel-file-management-image-configure');
    }

    private function registerCommands()
    {
        $this->commands([
            PublishCommand::class,
        ]);
    }
}
