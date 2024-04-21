<?php

namespace Sajadsdi\LaravelFileManagementImage\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Sajadsdi\LaravelFileManagement\Concerns\StorageToolsTrait;
use Sajadsdi\LaravelFileManagementImage\Exceptions\ImageNotSetInImageServiceException;
use Sajadsdi\LaravelFileManagementImage\Services\ImageService;

class ProcessImageBeforeUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, StorageToolsTrait;

    private array $heights;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $config, public string $tempPath, public array $file)
    {
        $this->onQueue($this->config['queue']);
    }

    /**
     * Execute the job.
     * @throws ImageNotSetInImageServiceException
     */
    public function handle(ImageService $service)
    {
        // save original before any process on image
        if ($this->config['save_original'] ?? false) {
            $this->putFile($this->file['disk'], str_replace('_fm', "_" . $this->config['original_suffix'] ?? "org", $this->file['path']), file_get_contents($this->tempPath));
        }

        //optimize and fix exif and update temp file
        file_put_contents($this->tempPath, $service->setImage($this->tempPath)->fixExifOrientation()->encode($this->file['ext'], $this->config['quality']));
    }

}
