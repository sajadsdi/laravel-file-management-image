<?php

namespace Sajadsdi\LaravelFileManagementImage\Listeners\Upload;

use Sajadsdi\LaravelFileManagement\Events\Upload\BeforeUpload;
use Sajadsdi\LaravelFileManagementImage\Jobs\Upload\ProcessImageBeforeUploadJob;

class BeforeUploadListener
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
    public function handle(BeforeUpload $event): void
    {
        if ($event->file['type'] == 'image') {
            ProcessImageBeforeUploadJob::dispatchSync(array_merge($event->config, config('file-management-image')), $event->tempPath, $event->file);
        }
    }
}
