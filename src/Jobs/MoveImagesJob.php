<?php

namespace Sajadsdi\LaravelFileManagementImage\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Sajadsdi\LaravelFileManagement\Concerns\StorageToolsTrait;
use Sajadsdi\LaravelFileManagement\Jobs\MoveFileJob;
use Sajadsdi\LaravelFileManagement\Jobs\Update\UpdateFileDetails;

class MoveImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, StorageToolsTrait;


    /**
     * Create a new job instance.
     */
    public function __construct(public array $config, public array $file, public string $newDisk, public string $newPath)
    {
        $this->onQueue($this->config['queue']);
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Move original image if exist.
        if (isset($this->file['details']['original'])) {
            MoveFileJob::dispatchSync($this->config, $this->file['details']['original']['disk'], $this->file['details']['original']['path'], $this->newDisk, $this->file['details']['original']['path']);

            $this->file['details']['original'] = ['disk' => $this->newDisk, 'path' => $this->file['details']['original']['path']];
        }

        // Move all resized images if exists.
        if (isset($this->file['details']['resize'])) {
            foreach ($this->file['details']['resize'] as $height => $val) {

                // Move file to trash
                MoveFileJob::dispatchSync($this->config, $val['disk'], $val['path'], $this->newDisk, $val['path']);

                $this->file['details']['resize'][$height]['path'] = $val['path'];
                $this->file['details']['resize'][$height]['disk'] = $this->newDisk;
            }
        }

        if($this->file['details']) {
            UpdateFileDetails::dispatchSync($this->file['id'], $this->file['details'], $this->config['queue']);
        }
    }


}
