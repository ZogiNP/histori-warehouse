<?php

return [
    'media' => [
        'disk' => 'warehouse_private',

        // Batas awal per foto: 5120 KiB = 5 MiB.
        'max_photo_size_kb' => (int) env(
            'WAREHOUSE_MAX_PHOTO_SIZE_KB',
            5120
        ),

        'allowed_photo_mime_types' => [
            'image/jpeg',
            'image/png',
            'image/webp',
        ],
    ],
];