<?php

use Zend\Mail\Transport\File;

return [
    'emailTransport' => [
        'type' => 'file',
        'options' => [
            'path' => realpath(__DIR__ . "/../../data/mails"),
            'callback' => function (File $transport) {
                return 'Message_' . microtime(true) . '_' . mt_rand() . '.txt';
            }
        ]
    ]
];
