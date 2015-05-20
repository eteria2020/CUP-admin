<?php
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
                ]
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
        ]
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\controller\Customers' => 'Application\Controller\CustomersController'
        ),
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
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),

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
            )
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
                ['controller' => 'Application\Controller\Customers', 'roles' => ['user']]
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
                ],
            ]
        ]
    ]
);
