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
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/tripscity/:id',
                            'constraints' => [
                                'id' => '[0-9]*'
                            ],
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
		                    'get-urban-areas' => [
		                        'type'    => 'Segment',
		                        'options' => [
				                    'route'    => '/get-urban-areas/:city',
		                            'constraints' => [
		                                'city' => '[0-9]*'
		                            ],
		                            'defaults' => [
		                                'action' => 'get-urban-areas',
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
	        'map' => [
		    	'assets-modules/reports/js/dc.js.map' => $baseDir. '/bower_components/dcjs/dc.js.map',
		    	'assets-modules/reports/js/dc.js' => $baseDir. '/bower_components/dcjs/dc.js',
	        ],
            'collections' => [
	            'assets-modules/reports/css/vendors.css' => [
                    'font-awesome/css/font-awesome.css',
                    'dcjs/dc.css',
                ],
				'assets-modules/reports/js/vendors.js' => [
					'jquery/dist/jquery.js',
					'assets-modules/reports/js/menu.js',
					'crossfilter/crossfilter.js',
					'd3/d3.js',
					'dcjs/dc.js',
				],
            ],
            'paths' => [
                'reports' => __DIR__ . '/../public',
                $baseDir. '/bower_components',
            ],
        ],
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
