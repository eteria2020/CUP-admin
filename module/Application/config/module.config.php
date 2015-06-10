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
                        'controller' => 'Application\Controller\Customers',
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
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Customers',
                                'action'        => 'datatable',
                            ],
                        ],
                    ],
                    'edit' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/edit/:id',
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Customers',
                                'action'        => 'edit',
                            ],
                        ],
                    ],
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
                            'route'    => '/delete/:plate/:type',
                            'constraints' => array(
                                'plate' => '[A-Z0-9]*'
                            ),
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Cars',
                                'action'        => 'delete',
                            ],
                        ],
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
            ]
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => [
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'CustomerForm' => 'Application\Form\CustomerFormFactory',
            'UserForm' => 'Application\Form\UserFormFactory',
            'CarForm' => 'Application\Form\CarFormFactory',
        ]
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
        ),
        'factories' => [
            'Application\controller\Customers'    => 'Application\Controller\CustomersControllerFactory',
            'Application\Controller\ConsoleUser'  => 'Application\Controller\ConsoleUserControllerFactory',
            'Application\controller\Users'        => 'Application\Controller\UsersControllerFactory',
            'Application\Controller\Cars'         => 'Application\Controller\CarsControllerFactory',
            'Application\Controller\Reservations' => 'Application\Controller\ReservationsControllerFactory'
        ]
    ),
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
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
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

    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),
            'orm_default' => array(
                'class'   => 'Doctrine\ORM\Mapping\Driver\DriverChain',
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            ),
        ),
    ),

    // ACL
    'bjyauthorize' => array(
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                'admin' => [],
            ],
        ],
        'rule_providers' => [
            'BjyAuthorize\Provider\Rule\Config' => [
                'allow' => [
                    [['user'], 'admin']
                ],
            ],
        ],
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                // Enable access to ZFC User pages
                array('controller' => 'zfcuser', 'roles' => array()),
                array('controller' => 'Application\Controller\Index', 'roles' => array('user')),
                ['controller' => 'Application\Controller\Customers', 'roles' => ['user']],
                ['controller' => 'Application\Controller\Cars', 'roles' => ['user']],
                ['controller' => 'Application\Controller\ConsoleUser', 'roles' => []],
                ['controller' => 'Application\Controller\Users', 'roles' => ['user']],
                ['controller' => 'Application\Controller\Reservations', 'roles' => ['user']]
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
                ],
            ],
            [
                'label'     => 'Auto',
                'route'     => 'cars',
                'icon'      => 'fa fa-users',
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
            ]
        ]
    ]
);
