<?php
namespace Application;

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
$translator = new \Zend\I18n\Translator\Translator;

// Getting the siteroot path ( = sharengo-admin folder)
$baseDir = realpath(__DIR__.'/../../../');

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'customers' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/customers',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Customers',
                        'action' => 'list'
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'datatable' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/datatable',
                            'defaults' => [
                                'action'        => 'datatable',
                            ],
                        ],
                    ],
                    'edit' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/edit/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action' => 'edit',
                            ],
                        ],
                    ],
                    'remove-card' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/remove-card/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action'        => 'remove-card',
                            ],
                        ],
                    ],
                    'assign-card' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/assign-card/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action'        => 'assign-card',
                            ],
                        ],
                    ],
                    'list-card' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/card',
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Customers',
                                'action'        => 'list-card',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'add' => [
                                'type'    => 'Literal',
                                'options' => [
                                    'route'    => '/add',
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Application\Controller',
                                        'controller'    => 'Customers',
                                        'action'        => 'add-card',
                                    ],
                                ],
                            ],
                            'datatable' => [
                                'type'    => 'Literal',
                                'options' => [
                                    'route'    => '/datatable',
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Application\Controller',
                                        'controller'    => 'Customers',
                                        'action'        => 'list-cards-datatable',
                                    ],
                                ],
                            ],
                        ]
                    ],
                    'ajax-tab-info' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/ajax-tab/info/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action'        => 'info-tab',
                            ],
                        ],
                    ],
                    'ajax-tab-edit' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/ajax-tab/edit/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'controller' => 'CustomersEdit',
                                'action'        => 'edit-tab',
                            ],
                        ],
                    ],
                    'deactivate' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/deactivate/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'controller' => 'CustomersEdit',
                                'action' => 'deactivate',
                            ],
                        ],
                    ],
                    'reactivate' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/reactivate/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'controller' => 'CustomersEdit',
                                'action' => 'reactivate',
                            ],
                        ],
                    ],
                    'edit-deactivation' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/edit/deactivation/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'controller' => 'CustomersEdit',
                                'action' => 'edit-deactivation',
                            ],
                        ],
                    ],
                    'ajax-tab-bonus' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/ajax-tab/bonus/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action'        => 'bonus-tab',
                            ],
                        ],
                    ],
                    'ajax-tab-points' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/ajax-tab/points/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action'        => 'points-tab',
                            ],
                        ],
                    ],
                    'ajax-tab-card' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/ajax-tab/card/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action'        => 'card-tab',
                            ],
                        ],
                    ],
                    'ajax-tab-invoices' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/ajax-tab/invoices/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action'        => 'invoices-tab',
                            ],
                        ],
                    ],
                    'ajax-tab-contract' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/ajax-tab/contract/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action'        => 'contract-tab',
                            ],
                        ],
                    ],
                    'ajax-tab-notes' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/ajax-tab/notes/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'controller' => 'CustomerNote',
                                'action' => 'notes-tab',
                            ],
                        ],
                    ],
                    'add-note' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/note/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'controller' => 'CustomerNote',
                                'action'        => 'add-note',
                            ],
                        ],
                    ],
                    'ajax-tab-failure' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/ajax-tab/failure/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'controller' => 'CustomerFailure',
                                'action' => 'failure-tab',
                            ],
                        ],
                    ],
                    'ajax-tab-license' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/ajax-tab/license/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'controller' => 'CustomerLicense',
                                'action' => 'license-tab',
                            ],
                        ],
                    ],
                    'ajax-card-code' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/ajax-card-code-autocomplete',
                            'defaults' => [
                                'action'        => 'ajax-card-code-autocomplete',
                            ],
                        ],
                    ],
                    'assign-promo-code' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/assign-promo-code/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action'        => 'assign-promo-code',
                            ],
                        ],
                    ],
                    'assign-bonus-ajax' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/assign-bonus-ajax/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action' => 'assign-bonus-ajax',
                            ],
                        ],
                    ],
                    'add-bonus' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/add-bonus/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action'        => 'add-bonus',
                            ],
                        ],
                    ],
                    'add-points' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/add-points/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action'        => 'add-points',
                            ],
                        ],
                    ],
                    'remove-bonus' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/remove-bonus/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action'        => 'remove-bonus',
                            ],
                        ],
                    ],
                    'remove-point' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/remove-point/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action'        => 'remove-point',
                            ],
                        ],
                    ],
                    'activate' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/activate/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action' => 'activate',
                            ]
                        ]
                    ],
                    'info' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/info/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action' => 'info'
                            ]
                        ]
                    ],
                    'disable-contract' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/disable-contract',
                            'defaults' => [
                                'action' => 'disable-contract'
                            ]
                        ]
                    ],
                    'foreign-drivers-license' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/foreign-drivers-license',
                            'defaults' => [
                                'controller' => 'Application\Controller\ForeignDriversLicense',
                                'action' => 'uploaded-files'
                            ]
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'datatable' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/datatable',
                                    'defaults' => [
                                        'action' => 'datatable'
                                    ]
                                ]
                            ],
                            'download' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/download/:id',
                                    'defaults' => [
                                        'action' => 'download'
                                    ]
                                ]
                            ],
                            'validate' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/validate/:id',
                                    'defaults' => [
                                        'action' => 'validate'
                                    ]
                                ]
                            ],
                            'revoke' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/revoke/:id',
                                    'defaults' => [
                                        'action' => 'revoke'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ],
            'cars' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/cars',
                    'defaults' => [
                        'controller' => 'Application\Controller\Cars',
                        'action' => 'index'
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'datatable' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/datatable',
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Cars',
                                'action'        => 'datatable',
                            ],
                        ],
                    ],
                    'add' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Cars',
                                'action'        => 'add',
                            ],
                        ],
                    ],
                    'edit' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/edit/:plate',
                            'constraints' => [
                                'plate' => '[a-zA-Z0-9]*'
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Cars',
                                'action'        => 'edit',
                            ],
                        ],
                    ],
                    'delete' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/delete/:plate',
                            'constraints' => [
                                'plate' => '[A-Z0-9]*'
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Cars',
                                'action'        => 'delete',
                            ],
                        ],
                    ],
                    'send-command' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/send-command/:plate/:command',
                            'constraints' => [
                                'plate' => '[a-zA-Z0-9]*',
                                'command' => '[0-9]*'
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Cars',
                                'action'        => 'send-command',
                            ],
                        ],
                    ],
                    'ajax-tab-edit' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/ajax-tab/edit/:plate',
                            'constraints' => [
                                'plate' => '[a-zA-Z0-9]*',
                            ],
                            'defaults' => [
                                'action'        => 'edit-tab',
                            ],
                        ],
                    ],
                    'ajax-tab-commands' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/ajax-tab/commands/:plate',
                            'constraints' => [
                                'plate' => '[a-zA-Z0-9]*',
                            ],
                            'defaults' => [
                                'action'        => 'commands-tab',
                            ],
                        ],
                    ],
                    'ajax-tab-damages' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/ajax-tab/damages/:plate',
                            'constraints' => [
                                'plate' => '[a-zA-Z0-9]*',
                            ],
                            'defaults' => [
                                'action'        => 'damages-tab',
                            ],
                        ],
                    ],
                ],
            ],
            'invoices' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/invoices',
                    'defaults' => [
                        'controller' => 'Application\Controller\Invoices',
                        'action' => 'index'
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'datatable' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/datatable',
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Invoices',
                                'action'        => 'datatable',
                            ],
                        ],
                    ],
                ],
            ],
            'trips' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/trips',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Trips',
                        'action' => 'index'
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'datatable' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/datatable',
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'Trips',
                                'action'        => 'datatable',
                            ],
                        ],
                    ],
                    'not-payed-datatable' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/not-payed-datatable',
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'action' => 'datatable',
                                'controller' => 'TripsNotPayed'
                            ]
                        ]
                    ],
                    'cost' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/cost',
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'Trips',
                                'action' => 'trip-cost',
                            ],
                        ]
                    ],
                    'cost-computation' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/cost-computation',
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'Trips',
                                'action' => 'trip-cost-computation'
                            ]
                        ]
                    ],
                    'details' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/details/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'Trips',
                                'action' => 'details'
                            ]
                        ]
                    ],
                    'info-tab' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/tab/info/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'Trips',
                                'action' => 'info-tab'
                            ]
                        ]
                    ],
                    'cost-tab' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/tab/cost/:id',
                            'constraints' => [
                                'id'    => '[0-9]*'
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'Trips',
                                'action' => 'cost-tab'
                            ]
                        ]
                    ],
                    'edit-tab' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/tab/edit/:id',
                            'constraints' => [
                                'id'    => '[0-9]*'
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'EditTrip',
                                'action' => 'edit-tab'
                            ]
                        ]
                    ],
                    'close-tab' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/tab/close/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'Trips',
                                'action' => 'close-tab'
                            ]
                        ]
                    ],
                    'map-tab' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/tab/map/:id',
                            'constraints' => [
                                'id'    => '[0-9]*'
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'Trips',
                                'action' => 'map-tab'
                            ]
                        ]
                    ],
                    'do-close' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/do-close',
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'Trips',
                                'action' => 'do-close'
                            ]
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'post' => [
                                'type' => 'Method',
                                'options' => [
                                    'verb' => 'post'
                                ]
                            ]
                        ]
                    ],
                    'not-payed' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/not-payed',
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'action' => 'list',
                                'controller' => 'TripsNotPayed'
                            ]
                        ]
                    ]
                ],
            ],
            'zfcuser' => [
                'child_routes' => [
                    'register' => [
                        'options' => [
                            'defaults' => [
                                'controller' => null
                            ]
                        ]
                    ]
                ]
            ],
            'unauthorized' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/unauthorized',
                    'defaults' => [
                        'controller' => 'Application\Controller\Error',
                        'action'     => 'unauthorized',
                    ],
                ],
            ],
            'users' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/users',
                    'defaults' => [
                        'controller' => 'Application\Controller\Users',
                        'action' => 'index'
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'datatable' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/datatable',
                            'defaults' => [
                                'action' => 'datatable',
                            ],
                        ],
                    ],
                    'add' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/add',
                            'defaults' => [
                                'action' => 'add',
                            ],
                        ],
                    ],
                    'edit' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/edit/:id',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'action' => 'edit',
                            ],
                        ],
                    ],
                ],
            ],
            'reservations' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/reservations',
                    'defaults' => [
                        'controller' => 'Application\Controller\Reservations',
                        'action' => 'index'
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'datatable' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/datatable',
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Reservations',
                                'action'        => 'datatable',
                            ],
                        ],
                    ],
                ],
            ],
            'invoices' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/invoices',
                    'defaults' => [
                        'controller' => 'Application\Controller\Invoices',
                        'action' => 'index'
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'datatable' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/datatable',
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Invoices',
                                'action'        => 'datatable',
                            ],
                        ],
                    ],
                ],
            ],
            'zones' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/zones',
                    'defaults' => [
                        'controller' => 'Application\Controller\Zones',
                        'action' => 'index'
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'ajax-tab-list' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/ajax-tab-list',
                            'defaults' => [
                                'action' => 'list-tab',
                            ],
                        ],
                    ],
                    'zone-alarms' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/zone-alarms',
                            'defaults' => [
                                'action' => 'zone-alarms',
                            ],
                        ],
                    ],
                    'zone-bonus' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/zone-bonus',
                            'defaults' => [
                                'action' => 'zone-bonus',
                            ],
                        ],
                    ],
                    'ajax-tab-groups' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/ajax-tab-groups',
                            'defaults' => [
                                'action' => 'groups-tab',
                            ],
                        ],
                    ],
                    'ajax-tab-prices' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/ajax-tab-prices',
                            'defaults' => [
                                'action' => 'prices-tab',
                            ],
                        ],
                    ],
                    'datatable' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/datatable',
                            'defaults' => [
                                'action' => 'datatable',
                            ],
                        ],
                    ],
                    'edit' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/edit/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action' => 'edit',
                            ],
                        ],
                    ],
                ],
            ],
            'payments' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/payments',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Payments'
                    ]
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'retry' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/retry/:id',
                            'constraints' => [
                                'id'    => '[0-9]*'
                            ],
                            'defaults' => [
                                'action' => 'retry'
                            ]
                        ]
                    ],
                    'do-retry' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/do-retry/:id',
                            'constraints' => [
                                'id'    => '[0-9]*'
                            ],
                            'defaults' => [
                                'action' => 'do-retry'
                            ]
                        ]
                    ],
                    'failed-payments' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/failed-payments',
                            'defaults' => [
                                'action' => 'failed-payments'
                            ]
                        ]
                    ],
                    'failed-payments-datatable' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/failed-payments-datatable',
                            'defaults' => [
                                'action' => 'failed-payments-datatable'
                            ]
                        ]
                    ],
                    'extra' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/extra',
                            'defaults' => [
                                'action' => 'extra'
                            ]
                        ]
                    ],
                    'pay-extra' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/pay-extra',
                            'defaults' => [
                                'action' => 'pay-extra'
                            ]
                        ]
                    ],
                    'set-trip-as-payed' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/set-trip-payed/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action' => 'set-trip-as-payed-ajax'
                            ]
                        ]
                    ],
                    'recap' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/recap',
                            'defaults' => [
                                'action' => 'recap'
                            ]
                        ]
                    ],
                    'fares' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/fares',
                            'defaults' => [
                                'action' => 'fares'
                            ]
                        ]
                    ],
                    'csv' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/csv',
                            'defaults' => [
                                'controller' => 'PaymentsCsv',
                                'action' => 'csv'
                            ]
                        ]
                    ],
                    'csv-add-file' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/csv-add-file',
                            'defaults' => [
                                'controller' => 'PaymentsCsv',
                                'action' => 'add-file'
                            ]
                        ]
                    ],
                    'csv-analyze-file' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/csv-analyze-file',
                            'defaults' => [
                                'controller' => 'PaymentsCsv',
                                'action' => 'analyze-file'
                            ]
                        ]
                    ],
                    'csv-details' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/csv-details/:id',
                            'constraints' => [
                                'id'    => '[0-9]*'
                            ],
                            'defaults' => [
                                'controller' => 'PaymentsCsv',
                                'action' => 'details'
                            ]
                        ]
                    ],
                    'csv-add-note' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/csv-add-note/:id',
                            'constraints' => [
                                'id'    => '[0-9]*'
                            ],
                            'defaults' => [
                                'controller' => 'PaymentsCsv',
                                'action' => 'add-note'
                            ]
                        ]
                    ],
                    'csv-resolve' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/csv-resolve/:id',
                            'constraints' => [
                                'id'    => '[0-9]*'
                            ],
                            'defaults' => [
                                'controller' => 'PaymentsCsv',
                                'action' => 'resolve'
                            ]
                        ]
                    ],
                    'csv-upload' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/csv-upload',
                            'defaults' => [
                                'controller' => 'PaymentsCsv',
                                'action' => 'upload'
                            ]
                        ]
                    ]
                ]
            ],
            'configurations' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/configurations',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Configurations'
                    ]
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'manage-alarm' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/manage-alarm/',
                            'defaults' => [
                                'action' => 'manageAlarm'
                            ]
                        ]
                    ],
                    'manage-pois' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/manage-pois',
                            'defaults' => [
                                'controller' => 'Pois',
                                'action' => 'index'
                            ]
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'datatable' => [
                                'type'    => 'Literal',
                                'options' => [
                                    'route'    => '/datatable',
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Application\Controller',
                                        'controller'    => 'Pois',
                                        'action'        => 'datatable',
                                    ],
                                ],
                            ],
                            'add' => [
                                'type'    => 'Literal',
                                'options' => [
                                    'route'    => '/add',
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Application\Controller',
                                        'controller'    => 'Pois',
                                        'action'        => 'add',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/edit/:id',
                                    'constraints' => [
                                        'plate' => '[0-9]*'
                                    ],
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Application\Controller',
                                        'controller'    => 'Pois',
                                        'action'        => 'edit',
                                    ],
                                ],
                            ],
                            'delete' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/delete/:id',
                                    'constraints' => [
                                        'plate' => '[0-9]*'
                                    ],
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Application\Controller',
                                        'controller'    => 'Pois',
                                        'action'        => 'delete',
                                    ],
                                ],
                            ],
                        ]
                    ]
                ]
            ],
            'cars-configurations' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/cars-configurations',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'CarsConfigurations',
                        'action'        => 'list-all'
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'datatable' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/datatable',
                            'defaults' => [
                                'action'        => 'datatable',
                            ],
                        ],
                    ],
                    'add' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/add',
                            'defaults' => [
                                'action' => 'add'
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/list',
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'all' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/all',
                                    'defaults' => [
                                        'action' => 'list-all'
                                    ],
                                ],
                            ],
                            'global' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/global',
                                    'defaults' => [
                                        'action' => 'list-global'
                                    ],
                                ],
                            ],
                            'fleet' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/fleet',
                                    'defaults' => [
                                        'action' => 'list-fleet'
                                    ],
                                ],
                            ],
                            'model' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/model',
                                    'defaults' => [
                                        'action' => 'list-model'
                                    ],
                                ],
                            ],
                            'car' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/car',
                                    'defaults' => [
                                        'action' => 'list-car'
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'edit' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/edit/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'CarsConfigurations',
                                'action'        => 'edit',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'ajax-edit' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/ajax-edit/:optionid',
                                    'constraints' => [
                                        'optionid' => '[a-zA-Z0-9]*',
                                    ],
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Application\Controller',
                                        'controller'    => 'CarsConfigurations',
                                        'action'        => 'ajax-get-option',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'details' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/details/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action' => 'details'
                            ],
                        ],
                    ],
                    'delete' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/delete/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'CarsConfigurations',
                                'action'        => 'delete',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'delete-option' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/:optionid',
                                    'constraints' => [
                                        'optionid' => '[a-zA-Z0-9]*',
                                    ],
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Application\Controller',
                                        'controller'    => 'CarsConfigurations',
                                        'action'        => 'delete-option',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'notifications' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/notifications',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Notifications',
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'details' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/details/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action' => 'details'
                            ]
                        ]
                    ],
                    'datatable' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/datatable',
                            'defaults' => [
                                'action' => 'datatable',
                            ],
                        ],
                    ],
                    'ajax-acknowledgment' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/acknowledgment/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
                            'defaults' => [
                                'action' => 'ajax-acknowledgment',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'aliases' => [
            'translator' => 'MvcTranslator',
            'Zend\Authentication\AuthenticationService' => 'zfcuser_auth_service'
        ],
        'factories' => [
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'CustomerForm' => 'Application\Form\CustomerFormFactory',
            'UserForm' => 'Application\Form\UserFormFactory',
            'CarForm' => 'Application\Form\CarFormFactory',
            'PoiForm' => 'Application\Form\PoiFormFactory',
            'CardForm' => 'Application\Form\CardFormFactory',
            'DriverForm' => 'Application\Form\DriverFormFactory',
            'SettingForm' => 'Application\Form\SettingFormFactory',
            'PromoCodeForm' => 'Application\Form\PromoCodeFormFactory',
            'CustomerBonusForm' => 'Application\Form\CustomerBonusFormFactory',
            'CustomerPointForm' => 'Application\Form\CustomerPointFormFactory',
            'TripCostForm' => 'Application\Form\TripCostFormFactory',
            'FaresForm' => 'Application\Form\FaresFormFactory',
            'EditTripForm' => 'Application\Form\EditTripFormFactory',
            'ConfigurationsForm' => 'Application\Form\ConfigurationsFormFactory',
            'CarsConfigurationsForm' => 'Application\Form\CarsConfigurationsFormFactory',
            'ChangeLanguageDetector.listener' => 'Application\Listener\ChangeLanguageDetectorFactory',
            'ZoneForm' => 'Application\Form\ZoneFormFactory',
        ]
    ],
    'asset_manager' => [
        'resolver_configs' => [
            'collections' => [
                // JavaScript
                'assets-modules/js/vendor.zones.js' => [
                    // Libs
                    'ol3/ol.js',
                    'bootstrap-switch/dist/js/bootstrap-switch.js',
                ],
                'assets-modules/js/vendor.notifications.js' => [
                    // Libs
                    'moment/min/moment.min.js',
                    'moment-timezone/builds/moment-timezone-with-data-2010-2020.min.js',
                    // Code
                    'assets-modules/application/js/notifications.js',
                ],
                'assets-modules/js/vendor.notifications.details.js' => [
                    // Libs
                    'ol3/ol.js',
                    'moment/min/moment.min.js',
                    'moment-timezone/builds/moment-timezone-with-data-2010-2020.min.js',
                    // Code
                    'assets-modules/application/js/notifications.details.js',
                ],
                // CSS
                'assets-modules/css/vendor.zones.css' => [
                    // Libs
                    'ol3/ol.css',
                    'bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css',
                ],
                'assets-modules/css/vendor.notifications.details.css' => [
                    // Libs
                    'ol3/ol.css',
                ],
                'js/trips.js' => [
                    'js/private-trips.js',
                ],
                'css/trips.css' => [],
            ],
            'paths' => [
                'application' => __DIR__.'/../public',
                $baseDir.'/bower_components',
            ],
        ],
        'filters' => [
            // Minify All JS
            'js' => [
                [
                    'filter' => 'JSMin',
                ],
            ],
            // Minify All CSS
            'css' => [
                [
                    'filter' => 'CssMin',
                ],
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Error' => 'Application\Controller\ErrorController',
        ],
        'factories' => [
            'Application\Controller\ConsoleUser' => 'Application\Controller\ConsoleUserControllerFactory',
            'Application\Controller\Trips' => 'Application\Controller\TripsControllerFactory',
            'Application\controller\Users' => 'Application\Controller\UsersControllerFactory',
            'Application\Controller\Cars' => 'Application\Controller\CarsControllerFactory',
            'Application\Controller\Customers' => 'Application\Controller\CustomersControllerFactory',
            'Application\Controller\Reservations' => 'Application\Controller\ReservationsControllerFactory',
            'Application\Controller\Invoices' => 'Application\Controller\InvoicesControllerFactory',
            'Application\Controller\Payments' => 'Application\Controller\PaymentsControllerFactory',
            'Application\Controller\CustomerNote' => 'Application\Controller\CustomerNoteControllerFactory',
            'Application\Controller\Configurations' => 'Application\Controller\ConfigurationsControllerFactory',
            'Application\Controller\CarsConfigurations' => 'Application\Controller\CarsConfigurationsControllerFactory',
            'Application\Controller\Pois' => 'Application\Controller\PoisControllerFactory',
            'Application\Controller\Zones' => 'Application\Controller\ZonesControllerFactory',
            'Application\Controller\CustomersEdit' => 'Application\Controller\CustomersEditControllerFactory',
            'Application\Controller\EditTrip' => 'Application\Controller\EditTripControllerFactory',
            'Application\Controller\CustomerFailure' => 'Application\Controller\CustomerFailureControllerFactory',
            'Application\Controller\CustomerLicense' => 'Application\Controller\CustomerLicenseControllerFactory',
            'Application\Controller\PaymentsCsv' => 'Application\Controller\PaymentsCsvControllerFactory',
            'Application\Controller\ForeignDriversLicense' => 'Application\Controller\ForeignDriversLicenseControllerFactory',
            'Application\Controller\TripsNotPayed' => 'Application\Controller\TripsNotPayedControllerFactory',
            'Application\Controller\Notifications' => 'Application\Controller\NotificationsControllerFactory',
        ]
    ],
    'controller_plugins' => [
        'factories' => [
            'TranslatorPlugin' => 'Application\Controller\Plugin\TranslatorPluginFactory'
        ]
    ],
    'input_filters' => [
        'invokables' => [
            'close-trip' => 'Application\Form\InputFilter\CloseTripFilter'
        ]
    ],
    'translator' => [
        'locale' => 'it_IT',
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo'
            ],
            [
                'type' => 'phparray',
                'base_dir' => __DIR__. '/../language/validator',
                'pattern' => '%s.php'
            ]
        ]
    ],
    'translation_config' => [
        'languages' => [
            'it' => [
                "locale" => "it_IT",
                "lang" => "it",
                "lang_3chars" => "ita",
                "label" => "Italiano"
            ],
            'en' => [
                "locale" => "en_US",
                "lang" => "en",
                "lang_3chars" => "eng",
                "label" => "English"
            ],
            'fr' => [
                "locale" => "fr_FR",
                "lang" => "fr",
                "lang_3chars" => "fra",
                "label" => "Français"
            ],
            'zh' => [
                "locale" => "zh_CN",
                "lang" => "zh",
                "lang_3chars" => "zho",
                "label" => "中国"
            ],
            'de' => [
                "locale" => "de_DE",
                "lang" => "de",
                "lang_3chars" => "deu",
                "label" => "Deutsch"
            ],
            'es' => [
                "locale" => "es_ES",
                "lang" => "es",
                "lang_3chars" => "spa",
                "label" => "Español"
            ],
            'hu' => [
                "locale" => "hu_HU",
                "lang" => "hu",
                "lang_3chars" => "hun",
                "label" => "Magyar"
            ],
            'pl' => [
                "locale" => "pl_PL",
                "lang" => "pl",
                "lang_3chars" => "pol",
                "label" => "Polskie"
            ],
            'pt' => [
                "locale" => "pt_PT",
                "lang" => "pt",
                "lang_3chars" => "por",
                "label" => "Português"
            ],
            'ru' => [
                "locale" => "ru_RU",
                "lang" => "ru",
                "lang_3chars" => "rus",
                "label" => "Pусский"
            ],
            'tr' => [
                "locale" => "tr_TR",
                "lang" => "tr",
                "lang_3chars" => "tur",
                "label" => "Türk"
            ]
        ],
        "language_folder" => __DIR__ . "/../language"
    ],
    'language_detector_listeners' => [
        'factories' => [
            'LanguageFromSessionDetectorListener' => 'Application\Listener\LanguageFromSessionDetectorListenerFactory'
        ]
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
            'error/unauthorized'      => __DIR__ . '/../view/error/unauthorized.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],

    'view_helpers'    => [
        'invokables' => [
            'CarStatus' => 'Application\View\Helper\CarStatus',
            'CarConfigurationPriorityType' => 'Application\View\Helper\CarConfigurationPriorityType',
        ],
        'factories' => [
            'datatableFilters' => 'Application\View\Helper\DatatableFiltersHelperFactory',
        ],
    ],

    // Placeholder for console routes
    'console' => [
        'router' => [
            'routes' => [
                'register' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'register [<email>]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ConsoleUser',
                            'action' => 'register'
                        ]
                    ]
                ]
            ],
        ],
    ],

    // ACL
    'bjyauthorize' => [
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [

                // current roles mapped as resourcers (used for navigation ACL's)
                'admin' => [],
                'callcenter' => [],
                'superadmin' => [],

                // other resources
                'customer' => [],
            ],
        ],
        'rule_providers' => [
            'BjyAuthorize\Provider\Rule\Config' => [
                'allow' => [
                    // for navigation
                    [['superadmin','admin'], 'admin'],
                    [['superadmin','admin','callcenter'], 'callcenter'],
                    [['superadmin'], 'superadmin'],

                    // for limiting certains operations
                    [['superadmin','admin'], 'customer', 'changeEmail'],
                    [['superadmin'], 'customer', 'userArea'],
                    [['superadmin'], 'customer', 'discountRate'],
                    [['superadmin'], 'customer', 'maintainer'],
                    [['superadmin'], 'customer', 'goldList'],
                ],
            ],
        ],
        'guards' => [
            'BjyAuthorize\Guard\Controller' => [
                // Enable access to ZFC User pages
                ['controller' => 'zfcuser', 'roles' => []],

                ['controller' => 'Application\Controller\Error', 'roles' => []],
                ['controller' => 'Application\Controller\Index', 'roles' => ['admin','callcenter']],
                ['controller' => 'Application\Controller\Customers', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\Trips', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\Cars', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\ConsoleUser', 'roles' => []],
                ['controller' => 'Application\Controller\Users', 'roles' => ['superadmin']],
                ['controller' => 'Application\Controller\Reservations', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\Invoices', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\Payments', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\CustomerNote', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\Configurations', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\CarsConfigurations', 'roles' => ['superadmin']],
                ['controller' => 'Application\Controller\Pois', 'roles' => ['superadmin']],
                ['controller' => 'Application\Controller\Zones', 'roles' => ['superadmin']],
                ['controller' => 'Application\Controller\CustomersEdit', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\EditTrip', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\CustomerFailure', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\CustomerLicense', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\PaymentsCsv', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\ForeignDriversLicense', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\TripsNotPayed', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\Notifications', 'roles' => ['admin','callcenter']],
            ],
        ],
    ],

    // navigation

    'navigation' => [
        'default' => [
            [
                'label'     => $translator->translate('Clienti'),
                'route'     => 'customers',
                'icon'      => 'fa fa-users',
                'resource'  => 'admin',
                'isRouteJs' => true,
                'pages'     => [
                    [
                        'label' => $translator->translate('Elenco'),
                        'route' => 'customers',
                        'isVisible' => true
                    ],
                    [
                        'label' => $translator->translate('Card'),
                        'route' => 'customers/list-card',
                        'isVisible' => true
                    ],
                    [
                        'label' => $translator->translate('Patenti estere'),
                        'route' => 'customers/foreign-drivers-license',
                        'isVisible' => true
                    ],
                    [
                        'route' => 'customers/edit',
                        'isVisible' => false
                    ],
                    [
                        'route' => 'customers/assign-bonus',
                        'isVisible' => false
                    ],
                    [
                        'route' => 'customers/add-bonus',
                        'isVisible' => false
                    ]
                ],
            ],
            [
                'label'     => $translator->translate('Auto'),
                'route'     => 'cars',
                'icon'      => 'fa fa-car',
                'resource'  => 'admin',
                'isRouteJs' => true,
                'pages'     => [
                    [
                        'label' => $translator->translate('Elenco'),
                        'route' => 'cars',
                        'isVisible' => true
                    ],
                    [
                        'route' => 'cars/add',
                        'isVisible' => false
                    ],
                    [
                        'route' => 'cars/edit',
                        'isVisible' => false
                    ],
                    [
                        'route' => 'cars/delete',
                        'isVisible' => false
                    ],
                ],
            ],
            [
                'label'     => $translator->translate('Corse'),
                'route'     => 'trips',
                'icon'      => 'fa fa-road',
                'resource'  => 'admin',
                'isRouteJs' => true,
                'pages'     => [
                    [
                        'label' => $translator->translate('Elenco'),
                        'route' => 'trips',
                        'isVisible' => true
                    ],
                    [
                        'label' => $translator->translate('Costo corsa'),
                        'route' => 'trips/cost',
                        'isVisible' => true
                    ],
                    [
                        'label' => $translator->translate('Corse non pagate'),
                        'route' => 'trips/not-payed',
                        'isVisible' => true
                    ]
                ],
            ],
            [
                'label'     => $translator->translate('Prenotazioni'),
                'route'     => 'reservations',
                'icon'      => 'fa fa-calendar',
                'resource'  => 'admin',
                'isRouteJs' => true,
                'pages'     => [
                    [
                        'label' => $translator->translate('Elenco'),
                        'route' => 'reservations',
                        'isVisible' => true
                    ]
                ],
            ],
            [
                'label'     => $translator->translate('Fatture'),
                'route'     => 'invoices',
                'icon'      => 'fa fa-file-o',
                'resource'  => 'admin',
                'isRouteJs' => true,
                'pages'     => [
                    [
                        'label' => $translator->translate('Elenco'),
                        'route' => 'invoices',
                        'isVisible' => true
                    ]
                ],
            ],
            [
                'label' => $translator->translate('Pagamenti'),
                'route' => 'payments',
                'icon' => 'fa fa-money',
                'resource' => 'admin',
                'isRouteJs' => true,
                'pages' => [
                    [
                        'label' => $translator->translate('Pagamenti falliti'),
                        'route' => 'payments/failed-payments',
                        'isVisible' => true
                    ],
                    [
                        'label' => $translator->translate('Addebita penale/extra'),
                        'route' => 'payments/extra',
                        'isVisible' => true
                    ],
                    [
                        'label' => $translator->translate('Competenze'),
                        'route' => 'payments/recap',
                        'isVisible' => true
                    ],
                    [
                        'label' => $translator->translate('Verifica CartaSI'),
                        'route' => 'payments/csv',
                        'isVisible' => true
                    ]
                ]
            ],
            [
                'label' => $translator->translate('Configurazione'),
                'route' => 'configurations',
                'icon' => 'fa fa-cog',
                'resource' => 'superadmin',
                'isRouteJs' => true,
                'pages' => [
                    [
                        'label' => $translator->translate('Gestione soglie allarme'),
                        'route' => 'configurations/manage-alarm',
                        'isVisible' => true
                    ],
                    [
                        'label' => $translator->translate('Gestione POIS'),
                        'route' => 'configurations/manage-pois',
                        'isVisible' => true
                    ],
                    [
                        'label' => $translator->translate('Tariffe'),
                        'route' => 'payments/fares',
                        'isVisible' => true
                    ],
                ]
            ],
            [
                'label' => $translator->translate('Configurazione Auto'),
                'route' => 'cars-configurations',
                'icon' => 'fa fa-cogs',
                'resource' => 'admin',
                'isRouteJs' => true,
                'pages' => [
                    [
                        'label' => $translator->translate('Elenco Completo'),
                        'route' => 'cars-configurations/list/all',
                        'isVisible' => true
                    ],
                    [
                        'label' => $translator->translate('Elenco Globale'),
                        'route' => 'cars-configurations/list/global',
                        'isVisible' => true
                    ],
                    [
                        'label' => $translator->translate('Elenco per Citta\''),
                        'route' => 'cars-configurations/list/fleet',
                        'isVisible' => true
                    ],
                    [
                        'label' => $translator->translate('Elenco per Modello / Citta\''),
                        'route' => 'cars-configurations/list/model',
                        'isVisible' => true
                    ],
                    [
                        'label' => $translator->translate('Elenco per Auto'),
                        'route' => 'cars-configurations/list/car',
                        'isVisible' => true
                    ],
                ],
            ],
            [
                'label'           => $translator->translate('Aree'),
                'route'           => 'zones',
                'icon'            => 'fa fa-map-marker',
                'resource'        => 'superadmin',
                'isRouteJs'       => true,
                'pages' => [
                    [
                        'label' => $translator->translate('Gestione aree'),
                        'route' => 'zones',
                        'isVisible' => true
                    ],
                    [
                        'label' => $translator->translate('Aree d\'allarme'),
                        'route' => 'zones/zone-alarms',
                        'isVisible' => true
                    ],
                    [
                        'label' => $translator->translate('Aree bonus'),
                        'route' => 'zones/zone-bonus',
                        'isVisible' => true
                    ]
                ]
            ],
            [
                'label' => $translator->translate('Notifiche'),
                'route' => 'notifications',
                'icon' => 'fa fa-bell-o',
                'resource' => 'admin',
                'isRouteJs' => true,
                'pages' => [
                    [
                        'label' => $translator->translate('Elenco'),
                        'route' => 'notifications',
                        'isVisible' => true
                    ]
                ]
            ],
            [
                'label'           => $translator->translate('Statistiche'),
                'icon'            => 'fa fa-line-chart',
                'resource'        => 'superadmin',
                'openOnNewWindow' => true,
                'route'           => 'reports',
            ],
            [
                'label'           => $translator->translate('Call center'),
                'route'           => 'call-center',
                'icon'            => 'fa fa-map-marker',
                'resource'        => 'callcenter',
                'openOnNewWindow' => true,
            ],
            [
                'label'           => $translator->translate('Mappa Allarmi'),
                'route'           => 'alarms',
                'icon'            => 'fa fa-map-marker',
                'resource'        => 'callcenter',
                'openOnNewWindow' => true,
            ],
        ]
    ],
];
