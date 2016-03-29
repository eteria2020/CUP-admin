<?php

return [
    'emailTransport' => [
        'type' => 'file',
        'filePath' => realpath(__DIR__ . "/../../data/mails")
    ],
];
