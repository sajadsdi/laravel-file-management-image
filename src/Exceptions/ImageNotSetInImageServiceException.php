<?php

namespace Sajadsdi\LaravelFileManagementImage\Exceptions;


class ImageNotSetInImageServiceException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Image is not set, please set image in ImageService before any action!');
    }
}
