<?php

use SimpleSoftwareIO\QrCode\Generator;

return [
    'default' => 'png',

    'drivers' => [
        'image' => Generator::class,
        'svg' => Generator::class,
    ],
];