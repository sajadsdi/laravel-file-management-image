<?php


if (!function_exists('get_image_size')) {
    /**
     * @param string|null $name
     * @param mixed|null $value
     * @return mixed|Seo
     * @throws BindingResolutionException
     */
    function get_image_size(string $path = null, int $size = 300): string
    {
        return str_replace('_fm', '_' . $size, $path);
    }
}
