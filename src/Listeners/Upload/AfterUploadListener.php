<?php

namespace Sajadsdi\LaravelFileManagementImage\Listeners\Upload;

use Sajadsdi\LaravelFileManagement\Events\Upload\AfterUpload;
use Sajadsdi\LaravelFileManagementImage\Jobs\Upload\ProcessImageAfterUploadJob;

class AfterUploadListener
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
    public function handle(AfterUpload $event): void
    {
        if ($event->file['type'] == 'image') {
            ProcessImageAfterUploadJob::dispatchSync(array_merge($event->config, config('file-management-image')), $event->tempPath, $event->file);
        }
    }
}
