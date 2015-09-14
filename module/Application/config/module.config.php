<?php
namespace Application;

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
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
                            'constraints' => array(
                                'id' => '[0-9]*'
                            ),
                            'defaults' => [
                                'action'        => 'edit',
                            ],
                        ],
                    ],
                    'remove-card' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/remove-card/:id',
                            'constraints' => array(
                                'id' => '[0-9]*'
                            ),
                            'defaults' => [
                                'action'        => 'remove-card',
                            ],
                        ],
                    ],
                    'assign-card' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/assign-card/:id',
                            'constraints' => array(
                                'id' => '[0-9]*'
                            ),
                            'defaults' => [
                                'action'        => 'assign-card',
                            ],
                        ],
                    ],
                    'ajax-tab-info' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/ajax-tab/info/:id',
                            'constraints' => array(
                                'id' => '[0-9]*'
                            ),
                            'defaults' => [
                                'action'        => 'info-tab',
                            ],
                        ],
                    ],
                    'ajax-tab-edit' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/ajax-tab/edit/:id',
                            'constraints' => array(
                                'id' => '[0-9]*'
                            ),
                            'defaults' => [
                                'action'        => 'edit-tab',
                            ],
                        ],
                    ],
                    'ajax-tab-bonus' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/ajax-tab/bonus/:id',
                            'constraints' => array(
                                'id' => '[0-9]*'
                            ),
                            'defaults' => [
                                'action'        => 'bonus-tab',
                            ],
                        ],
                    ],
                    'ajax-tab-card' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/ajax-tab/card/:id',
                            'constraints' => array(
                                'id' => '[0-9]*'
                            ),
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
                            'constraints' => array(
                                'plate' => '[a-zA-Z0-9]*'
                            ),
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
                            'constraints' => array(
                                'plate' => '[A-Z0-9]*'
                            ),
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
                            'constraints' => array(
                                'plate' => '[a-zA-Z0-9]*',
                                'command' => '[0-9]*'
                            ),
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Cars',
                                'action'        => 'send-command',
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
                                'action'        => 'datatable',
                            ],
                        ],
                    ],
                    'cost' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/cost',
                            'defaults' => [
                                'action' => 'trip-cost',
                            ],
                        ]
                    ],
                    'cost-computation' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/cost-computation',
                            'defaults' => [
                                'action' => 'trip-cost-computation'
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
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'add' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Users',
                                'action'        => 'add',
                            ],
                        ],
                    ],
                    'edit' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/edit/:id',
                            'constraints' => [
                                'id'    => '[0-9]*'
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Users',
                                'action'        => 'edit',
                            ],
                        ],
                    ]
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
                    ]
                ]
            ]
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        'factories' => [
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'CustomerForm' => 'Application\Form\CustomerFormFactory',
            'UserForm' => 'Application\Form\UserFormFactory',
            'CarForm' => 'Application\Form\CarFormFactory',
            'DriverForm' => 'Application\Form\DriverFormFactory',
            'SettingForm' => 'Application\Form\SettingFormFactory',
            'PromoCodeForm' => 'Application\Form\PromoCodeFormFactory',
            'CustomerBonusForm' => 'Application\Form\CustomerBonusFormFactory',
            'TripCostForm' => 'Application\Form\TripCostFormFactory',
            'ExtraPaymentsForm' => 'Application\Form\ExtraPaymentsFormFactory'
        ]
    ),
    'controllers' => [
        'invokables' => [
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Error' => 'Application\Controller\ErrorController',
        ],
        'factories' => [
            'Application\Controller\ConsoleUser'  => 'Application\Controller\ConsoleUserControllerFactory',
            'Application\Controller\Trips'        => 'Application\Controller\TripsControllerFactory',
            'Application\controller\Users'        => 'Application\Controller\UsersControllerFactory',
            'Application\Controller\Cars'         => 'Application\Controller\CarsControllerFactory',
            'Application\Controller\Customers'    => 'Application\Controller\CustomersControllerFactory',
            'Application\Controller\Reservations' => 'Application\Controller\ReservationsControllerFactory',
            'Application\Controller\Invoices' => 'Application\Controller\InvoicesControllerFactory',
            'Application\Controller\Payments' => 'Application\Controller\PaymentsControllerFactory'
        ]
    ],
    'translator'         => [
        'locale'                    => 'it',
        'translation_file_patterns' => [
            [
                'type'          => 'phpArray',
                'base_dir'      => 'vendor/zendframework/zendframework/resources/languages',
                'pattern'       => '%s/Zend_Validate.php',
                'text_domain'   => 'zend_validate',
            ]
        ],
    ],
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
            'error/unauthorized'      => __DIR__ . '/../view/error/unauthorized.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),

    'view_helpers'    => array(
        'invokables' => array(
            'CarStatus' => 'Application\View\Helper\CarStatus',
        )
    ),

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
    'bjyauthorize' => array(
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                'admin' => [],
                'callcenter' => [],
            ],
        ],
        'rule_providers' => [
            'BjyAuthorize\Provider\Rule\Config' => [
                'allow' => [
                    [['admin'], 'admin'],
                    [['admin','callcenter'], 'callcenter'],
                ],
            ],
        ],
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                // Enable access to ZFC User pages
                ['controller' => 'zfcuser', 'roles' => []],
                ['controller' => 'Application\Controller\Error', 'roles' => []],
                ['controller' => 'Application\Controller\Index', 'roles' => ['admin','callcenter']],
                ['controller' => 'Application\Controller\Customers', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\Trips', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\Cars', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\ConsoleUser', 'roles' => []],
                ['controller' => 'Application\Controller\Users', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\Reservations', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\Invoices', 'roles' => ['admin']],
                ['controller' => 'Application\Controller\Payments', 'roles' => ['admin']]
            ),
        ),
    ),

    // navigation
    'navigation' => [
        'default' => [
            [
                'label'     => 'Clienti',
                'route'     => 'customers',
                'icon'      => 'fa fa-users',
                'resource'  => 'admin',
                'isRouteJs' => true,
                'pages'     => [
                    [
                        'label' => 'Elenco',
                        'route' => 'customers',
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
                'label'     => 'Auto',
                'route'     => 'cars',
                'icon'      => 'fa fa-car',
                'resource'  => 'admin',
                'isRouteJs' => true,
                'pages'     => [
                    [
                        'label' => 'Elenco',
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
                'label'     => 'Corse',
                'route'     => 'trips',
                'icon'      => 'fa fa-road',
                'resource'  => 'admin',
                'isRouteJs' => true,
                'pages'     => [
                    [
                        'label' => 'Elenco',
                        'route' => 'trips',
                        'isVisible' => true
                    ],
                    [
                        'label' => 'Costo corsa',
                        'route' => 'trips/cost',
                        'isVisible' => true
                    ]
                ],
            ],
            [
                'label'     => 'Prenotazioni',
                'route'     => 'reservations',
                'icon'      => 'fa fa-calendar',
                'resource'  => 'admin',
                'isRouteJs' => true,
                'pages'     => [
                    [
                        'label' => 'Elenco',
                        'route' => 'reservations',
                        'isVisible' => true
                    ]
                ],
            ],
            [
                'label'     => 'Fatture',
                'route'     => 'invoices',
                'icon'      => 'fa fa-file-o',
                'resource'  => 'admin',
                'isRouteJs' => true,
                'pages'     => [
                    [
                        'label' => 'Elenco',
                        'route' => 'invoices',
                        'isVisible' => true
                    ]
                ],
            ],
            [
                'label' => 'Pagamenti',
                'route' => 'payments',
                'icon' => 'fa fa-money',
                'resource' => 'admin',
                'isRouteJs' => true,
                'pages' => [
                    [
                        'label' => 'Pagamenti falliti',
                        'route' => 'payments/failed-payments',
                        'isVisible' => true
                    ]/*,
                    [
                        'label' => 'Addebita penale/extra',
                        'route' => 'payments/extra',
                        'isVisible' => true
                    ]*/
                ]
            ],
            [
                'label'           => 'Call center',
                'route'           => 'call-center',
                'icon'            => 'fa fa-map-marker',
                'resource'        => 'callcenter',
                'openOnNewWindow' => true,
            ],
        ]
    ]
);
