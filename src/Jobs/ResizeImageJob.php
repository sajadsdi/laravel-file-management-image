<?php

namespace Sajadsdi\LaravelFileManagementImage\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Sajadsdi\LaravelFileManagement\Concerns\StorageToolsTrait;
use Sajadsdi\LaravelFileManagement\Jobs\Update\UpdateFileDetails;
use Sajadsdi\LaravelFileManagementImage\Exceptions\ImageNotSetInImageServiceException;
use Sajadsdi\LaravelFileManagementImage\Services\ImageService;

class ResizeImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, StorageToolsTrait;

    private array $heights;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $config, public string $tempPath, public array $file, array $customHeights = [])
    {
        $this->onQueue($this->config['queue']);
        $this->setHeights($customHeights);
    }

    /**
     * Execute the job.
     * @throws ImageNotSetInImageServiceException
     */
    public function handle(ImageService $service)
    {
        if (!$service->checkSetImage(false)) {
            $service->setImage($this->tempPath);
        }

        if ($this->config['resize_convert'] && $this->config['convert_ext']) {
            $this->file['path'] = str_replace('.' . $this->file['ext'], '.' . $this->config['convert_ext'], $this->file['path']);
            $this->file['ext'] = $this->config['convert_ext'];
        }

        $resizeDetails = [];

        foreach ($this->heights as $height) {
            $imageHeight = $service->getImage()->height();
            $resizePath = str_replace('_fm', '_' . $height, $this->file['path']);

            if ($imageHeight > $height) {
                $resizeDetails[$height]['path'] = $resizePath;
                $resizeDetails[$height]['disk'] = $this->file['disk'];

                $this->putFile($this->file['disk'], $resizePath, $service->resize($height)->encode($this->file['ext'], $this->config['quality']));

            } elseif ($this->config['resize_duplicate']) {
                $resizeDetails[$height]['path'] = $resizePath;
                $resizeDetails[$height]['disk'] = $this->file['disk'];

                $this->putFile($this->file['disk'], $resizePath, $service->encode($this->file['ext'], $this->config['quality']));
            }
        }

        if($resizeDetails) {
            UpdateFileDetails::dispatchSync($this->file['id'], ['resize' => $resizeDetails], $this->config['queue']);
        }
    }

    /**
     * @param array $customHeights
     * @return void
     */
    private function setHeights(array $customHeights): void
    {
        $this->heights = $customHeights ? $customHeights : $this->config['resize_heights'];

        rsort($this->heights);
    }

}
