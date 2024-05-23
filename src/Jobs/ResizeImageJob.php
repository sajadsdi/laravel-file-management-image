<?php

namespace Sajadsdi\LaravelFileManagementImage\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Sajadsdi\LaravelFileManagement\Concerns\StorageToolsTrait;
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

        if($this->config['resize_convert'] && $this->config['convert_ext']) {
            $this->file['path'] = str_replace('.'.$this->file['ext'] ,'.'.$this->config['convert_ext'] ,$this->file['path']);
            $this->file['ext'] = $this->config['convert_ext'];
        }

        foreach ($this->heights as $height) {
            $imageHeight = $service->getImage()->height();

            if ($imageHeight > $height) {
                $this->putFile($this->file['disk'], str_replace('_fm', '_' . $height, $this->file['path']), $service->resize($height)->encode($this->file['ext'], $this->config['quality']));
            } elseif ($this->config['resize_duplicate']) {
                $this->putFile($this->file['disk'], str_replace('_fm', '_' . $height, $this->file['path']), $service->encode($this->file['ext'], $this->config['quality']));
            }
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
