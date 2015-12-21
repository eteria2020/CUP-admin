<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

// Getting the siteroot path ( = sharengo-admin folder)
$baseDir = realpath(__DIR__ . '/../../../');

return array(
    'router' => array(
        'routes' => array(
            'reports' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/reports',
                    'defaults' => array(
                        'controller' => 'Reports\Controller\Index',
                        'action' => 'trips',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
		            'trips' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/trips',
                            'defaults' => [
                                'action' => 'trips',
                            ],
                        ],
                    ],
                    'map' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/map',
                            'defaults' => [
                                'action' => 'map',
                            ],
                        ],
                    ],
                    'live' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/live',
                            'defaults' => [
                                'action' => 'live',
                            ],
                        ],
                    ],
                    'routes' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/routes',
                            'defaults' => [
                                'action' => 'routes',
                            ],
                        ],
                    ],
                    'tripscity' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/tripscity',
                            'defaults' => [
                                'action' => 'tripscity',
                            ],
                        ],
                    ],
                    'api' => [
	                    'type'	=> 'Literal',
	                    'options'	=>	[
		                    'route'    => '/api',
		                    'defaults' => [
			                    'controller' => 'Reports\Controller\Api',
		                    ],
	                    ],
	                    'may_terminate' => true,
						'child_routes' => [
							'get-cities' => [
		                        'type'    => 'Literal',
		                        'options' => [
		                            'route'    => '/get-cities',
		                            'defaults' => [
		                                'action' => 'get-cities',
		                            ],
		                        ],
		                    ],
		                    'get-all-trips' => [
		                        'type'    => 'Literal',
		                        'options' => [
		                            'route'    => '/get-all-trips',
		                            'defaults' => [
		                                'action' => 'get-all-trips',
		                            ],
		                        ],
		                    ],
		                    'get-city-trips' => [
		                        'type'    => 'Literal',
		                        'options' => [
		                            'route'    => '/get-city-trips',
		                            'defaults' => [
		                                'action' => 'get-city-trips',
		                            ],
		                        ],
		                    ],	
						],
                    ],
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => [
            'Reports\Controller\Index' => 'Reports\Controller\IndexController',
        ],
        'factories' => [
			'Reports\Controller\Api' => 'Reports\Controller\ApiControllerFactory',
        ],
    ),
    
    'service_manager' => [
	    'factories' => [
			'Reports\Service\Reports' => 'Reports\Service\ReportsServiceFactory',    
	    ],
    ],
    
    'asset_manager' => [
        'resolver_configs' => [
            'collections' => [
	            'assets-modules/reports/css/vendors.css' => array(
                    'bootstrap/less/scaffolding.less',
                    //'bootstrap/dist/css/bootstrap.min.css',
                    //'font-awesome/css/font-awesome.css',
                ),
                
				'assets-modules/reports/js/vendors.js' => [
					'jquery/dist/jquery.js',
					'bootstrap/dist/js/bootstrap.js',
				]
            ],
            'paths' => array(
                'reports' => __DIR__ . '/../public',
                $baseDir. '/bower_components',
            ),
        ],
        'filters' => array(
	        // Converting less --> css
	        'assets-modules/reports/css/vendors.css' => [
		        [
			        'filter' => 'Lessphp',
		        ],
		    ],
        ),
    ],
	            

    'view_manager' => array(
        'template_map' => [
            'layout/reports'           => __DIR__ . '/../view/layout/layout.phtml',
        ],
        'template_path_stack' => array(
            __DIR__ . '/../view',
        )
    ),

    // ACL
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(

                ['controller' => 'Reports\Controller\Index', 'roles' => ['admin','callcenter']],
				['controller' => 'Reports\Controller\Api', 'roles' => ['admin','callcenter']],
            ),
        ),
    ),
);
