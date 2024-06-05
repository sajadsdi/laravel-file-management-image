<?php

namespace Sajadsdi\LaravelFileManagementImage\Listeners\Trash;

use Sajadsdi\LaravelFileManagement\Events\Trash\AfterTrash;
use Sajadsdi\LaravelFileManagementImage\Jobs\Trash\TrashImagesJob;

class AfterTrashListener
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
    public function handle(AfterTrash $event): void
    {
        if ($event->file['type'] == 'image') {
            TrashImagesJob::dispatchSync($event->config, $event->file);
        }
    }
}
