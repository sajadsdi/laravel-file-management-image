<?php

namespace Sajadsdi\LaravelFileManagementImage\Jobs\Upload;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Sajadsdi\LaravelFileManagement\Concerns\StorageToolsTrait;
use Sajadsdi\LaravelFileManagement\Jobs\Update\UpdateFileDetails;
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
            $orgPath = str_replace('_fm', "_" . $this->config['original_suffix'] ?? "org", $this->file['path']);

            $this->putFile($this->file['disk'], $orgPath, file_get_contents($this->tempPath));

            UpdateFileDetails::dispatchSync($this->file['id'], ['original' => ['disk' => $this->file['disk'], 'path' => $orgPath]], $this->config['queue']);
        }

        //optimize and fix exif and update temp file
        if ($this->config['fix_exif']) {
            file_put_contents($this->tempPath, $service->setImage($this->tempPath)->fixExifOrientation()->encode($this->file['ext'], $this->config['quality']));
        }
    }

}
