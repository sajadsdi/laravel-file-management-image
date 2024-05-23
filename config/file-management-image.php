<?php

return [
    'process_driver'     => 'gd',
    'resize'             => true,
    'resize_heights'     => [100, 150, 200, 300, 400],
    'resize_duplicate'   => true,
    'resize_convert'     => true,
    'convert_ext'        => 'webp',
    'fix_exif'           => true,
    'quality'            => 100,
    'save_original'      => false,
    'original_suffix'    => 'org',
    'process_to_queue'   => false,
    'queue'              => 'file-process-images',
];
