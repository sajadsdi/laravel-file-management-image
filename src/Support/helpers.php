<?php


if (!function_exists('get_image_size')) {
    /**
     * @param string $path
     * @param int $size 
     * @param string|null $type use type if enable convert on resize
     */
    function get_image_size(string $path, int $size = 300, ?string $type = null): string
    {
        $newPath = str_replace('_fm', '_' . $size, $path);

        if ($type) {
            $exdot = explode('.', $newPath);
            $oldType = $exdot[count($exdot) -1];

            $newPath = str_replace('.'.$oldType,'.'.$type, $newPath);
        }

        return $newPath;
    }
}
