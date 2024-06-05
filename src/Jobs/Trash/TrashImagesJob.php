<?php

namespace Sajadsdi\LaravelFileManagementImage\Jobs\Trash;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Sajadsdi\LaravelFileManagement\Jobs\MoveFileJob;
use Sajadsdi\LaravelFileManagement\Jobs\Update\UpdateFileDetails;

class TrashImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

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
        // Move original image to trash if exist.
        if (isset($this->file['details']['original'])) {
            $orgNewPath = $this->config['start_path'] . '/' . $this->file['details']['original']['path'];

            MoveFileJob::dispatchSync($this->config, $this->file['details']['original']['disk'], $this->file['details']['original']['path'], $this->file['disk'], $orgNewPath);

            $this->file['details']['original'] = ['disk' => $this->file['disk'], 'path' => $orgNewPath];
        }

        // Move all resized images if exists.
        if (isset($this->file['details']['resize'])) {
            foreach ($this->file['details']['resize'] as $height => $val) {

                $newPath = $this->config['start_path'] . '/' . $val['path'];

                // Move file to trash
                MoveFileJob::dispatchSync($this->config, $val['disk'], $val['path'], $this->file['disk'], $newPath);

                $this->file['details']['resize'][$height]['path'] = $newPath;
                $this->file['details']['resize'][$height]['disk'] = $this->file['disk'];
            }
        }

        if($this->file['details']) {
            UpdateFileDetails::dispatchSync($this->file['id'], $this->file['details'], $this->config['queue']);
        }
    }

}
