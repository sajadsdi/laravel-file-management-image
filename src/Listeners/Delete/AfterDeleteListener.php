<?php

namespace Sajadsdi\LaravelFileManagementImage\Listeners\Delete;


use Sajadsdi\LaravelFileManagement\Events\Delete\AfterDelete;
use Sajadsdi\LaravelFileManagementImage\Jobs\Delete\DeleteImagesJob;

class AfterDeleteListener
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
    public function handle(AfterDelete $event): void
    {
        if ($event->file['type'] == 'image') {
            DeleteImagesJob::dispatchSync($event->config, $event->file);
        }
    }
}
