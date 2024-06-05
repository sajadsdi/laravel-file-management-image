<?php

namespace Sajadsdi\LaravelFileManagementImage\Jobs\Delete;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Sajadsdi\LaravelFileManagement\Concerns\StorageToolsTrait;
use Sajadsdi\LaravelFileManagement\Jobs\Update\UpdateFileDetails;

class DeleteImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, StorageToolsTrait;

    private array $heights;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $config, public array $file)
    {
        $this->onQueue($this->config['queue']);
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Delete original image
        if (isset($this->file['details']['original'])) {
            $this->deleteFile($this->file['details']['original']['disk'], $this->file['details']['original']['path']);

            $this->file['details']['original'] = "The original image has been deleted!";
        }

        // delete all resized images
        if (isset($this->file['details']['resize'])) {

            foreach ($this->file['details']['resize'] as $height => $val) {
                $this->deleteFile($val['disk'], $val['path']);
            }

            $this->file['details']['resize'] = "All resized files were deleted!";
        }

        if ($this->file['details']) {
            UpdateFileDetails::dispatchSync($this->file['id'], $this->file['details'], $this->config['queue']);
        }
    }

}
