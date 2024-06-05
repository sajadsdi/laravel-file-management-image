<?php

namespace Sajadsdi\LaravelFileManagementImage\Listeners\Trash;

use Sajadsdi\LaravelFileManagement\Events\Trash\AfterRestoreTrash;
use Sajadsdi\LaravelFileManagementImage\Jobs\Trash\RestoreTrashImagesJob;

class AfterRestoreTrashListener
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
    public function handle(AfterRestoreTrash $event): void
    {
        if ($event->file['type'] == 'image') {
            RestoreTrashImagesJob::dispatchSync($event->config, $event->file);
        }
    }
}
