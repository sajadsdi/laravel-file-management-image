<?php

namespace Sajadsdi\LaravelFileManagementImage\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Sajadsdi\LaravelFileManagementImage\Services\ImageService;

class ProcessImageAfterUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $config, public string $tempPath, public array $file)
    {
        $this->onQueue($this->config['queue']);
    }

    /**
     * Execute the job.
     */
    public function handle(ImageService $service)
    {
        if ($this->config['resize']) {
            ResizeImageJob::dispatchSync($this->config, $this->tempPath, $this->file);
        }

        $service->unsetImage();
    }

}
