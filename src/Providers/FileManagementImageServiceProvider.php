<?php

namespace Sajadsdi\LaravelFileManagementImage\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Sajadsdi\LaravelFileManagement\Events\AfterMoveFile;
use Sajadsdi\LaravelFileManagement\Events\Delete\AfterDelete;
use Sajadsdi\LaravelFileManagement\Events\Trash\AfterRestoreTrash;
use Sajadsdi\LaravelFileManagement\Events\Trash\AfterTrash;
use Sajadsdi\LaravelFileManagement\Events\Upload\AfterUpload;
use Sajadsdi\LaravelFileManagement\Events\Upload\BeforeUpload;
use Sajadsdi\LaravelFileManagementImage\Console\PublishCommand;
use Sajadsdi\LaravelFileManagementImage\Listeners\AfterMoveFileListener;
use Sajadsdi\LaravelFileManagementImage\Listeners\Delete\AfterDeleteListener;
use Sajadsdi\LaravelFileManagementImage\Listeners\Trash\AfterRestoreTrashListener;
use Sajadsdi\LaravelFileManagementImage\Listeners\Trash\AfterTrashListener;
use Sajadsdi\LaravelFileManagementImage\Listeners\Upload\AfterUploadListener;
use Sajadsdi\LaravelFileManagementImage\Listeners\Upload\BeforeUploadListener;
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

        Event::listen(
            AfterTrash::class,
            AfterTrashListener::class
        );

        Event::listen(
            AfterRestoreTrash::class,
            AfterRestoreTrashListener::class
        );

        Event::listen(
            AfterDelete::class,
            AfterDeleteListener::class
        );

        Event::listen(
            AfterMoveFile::class,
            AfterMoveFileListener::class
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
