<?php

namespace Sajadsdi\LaravelFileManagementImage\Listeners;

use Sajadsdi\LaravelFileManagement\Events\AfterMoveFile;
use Sajadsdi\LaravelFileManagementImage\Jobs\MoveImagesJob;


class AfterMoveFileListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AfterMoveFile $event): void
    {
        if ($event->oldFile['type'] == 'image') {
            MoveImagesJob::dispatchSync($event->config, $event->oldFile, $event->movedDisk, $event->movedPath);
        }
    }
}
