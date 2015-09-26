<?php

namespace EventLogger;

return [

    'service_manager' => [
        'factories' => [
            'EventLogger\Listener\UserEventListener' => 'EventLogger\Listener\UserEventListenerFactory',
            'EventLogger\EventManager\EventManager' => 'EventLogger\EventManager\EventManagerFactory',
        ],
    ],
    'doctrine'        => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ],
            'orm_default'             => [
                'class'   => 'Doctrine\ORM\Mapping\Driver\DriverChain',
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ],
        ],
    ],

];
