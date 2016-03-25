<?php

return [
    'emailTransport' => [
        'type' => 'Zend\Mail\Transport\File',
        'filePath' => realpath(__DIR__ . "/../../data/mails")
    ],
];
