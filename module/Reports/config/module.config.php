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
		                    'get-trips-geo-data' => [
		                        'type'    => 'Literal',
		                        'options' => [
		                            'route'    => '/get-trips-geo-data',
		                            'defaults' => [
		                                'action' => 'get-trips-geo-data',
		                            ],
		                        ],
		                    ],
		                    'get-cars-geo-data' => [
		                        'type'    => 'Literal',
		                        'options' => [
		                            'route'    => '/get-cars-geo-data',
		                            'defaults' => [
		                                'action' => 'get-cars-geo-data',
		                            ],
		                        ],
		                    ],
		                    'get-trips' => [
		                        'type'    => 'Literal',
		                        'options' => [
		                            'route'    => '/get-trips',
		                            'defaults' => [
		                                'action' => 'get-trips',
		                            ],
		                        ],
		                    ],	                    
		                    'get-trips-from-logs' => [
		                        'type'    => 'Literal',
		                        'options' => [
		                            'route'    => '/get-trips-from-logs',
		                            'defaults' => [
		                                'action' => 'get-trips-from-logs',
		                            ],
		                        ],
		                    ],
		                    'get-trip-points-from-logs' => [
		                        'type'    => 'Literal',
		                        'options' => [
		                            'route'    => '/get-trip-points-from-logs',
		                            'defaults' => [
		                                'action' => 'get-trip-points-from-logs',
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
				
				// Specific Asset for Routes Page.
				'assets-modules/reports/js/vendor.routes.js' =>[
					'ol2/OpenLayers.js',					// OpenLayers
					'assets-modules/reports/js/OpenStreetMap.js',
       
					//'ol3/ol.js',
					
					'ol3/ol-debug.js',
					//'ol3-legacy/src/ol/featureoverlay.js',
       
					'jquery-legacy/dist/jquery.js',			// Jquery 1.11.3
					'jquery-migrate/jquery-migrate.js',		// Jquery Migrate 1.2.1
					                    
                    
                    'jquery.scrollTo/jquery.scrollTo.js',	// ScrollTo
                    'moment/moment.js',						// Moment.js
                    'eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
                    'seiyria-bootstrap-slider/js/bootstrap-slider.js',
                    
                    
					'assets-modules/reports/js/menu.js',
                    'assets-modules/reports/js/routes.js',
				],
				'assets-modules/reports/css/vendor.routes.css' => [
                    'eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css',
                    'seiyria-bootstrap-slider/dist/css/bootstrap-slider.css',
                    
					'ol3/ol.css',
					
	                'ol2/theme/default/style.css',
	                'ol2/theme/default/style.mobile.css',
	                
	                'assets-modules/reports/css/routes.css',
                ],
                
                
                 // Specific Asset for Trips Page.
				'assets-modules/reports/js/vendor.trips.main.js' =>[
					'jquery/dist/jquery.js',
					'crossfilter/crossfilter.js',
					'd3/d3.js',
					'dcjs/dc.js',
					 
					'assets-modules/reports/js/menu.js',
                    'assets-modules/reports/js/trips.main.js',
				],
                'assets-modules/reports/css/vendor.trips.main.css' => [
	                'font-awesome/css/font-awesome.css',
					'dcjs/dc.css',
                    
                    'assets-modules/reports/css/trips.main.css'
                ],
                
                // Specific Asset for Trips Page.
				'assets-modules/reports/js/vendor.trips.city.js' =>[
					'jquery/dist/jquery.js',
					'crossfilter/crossfilter.js',
					'd3/d3.js',
					'dcjs/dc.js',
					 
					'assets-modules/reports/js/menu.js',
                    'assets-modules/reports/js/trips.city.js',
				],
                'assets-modules/reports/css/vendor.trips.city.css' => [
	                'assets-modules/reports/css/vendor.trips.main.css'
                ],
                
                
                // Specific Asset for Live Page.
				'assets-modules/reports/js/vendor.live.js' =>[
					'jquery/dist/jquery.js',
                    'jquery.scrollTo/jquery.scrollTo.js',
                    'ol3/ol.js',
                    'jquery-ui/jquery-ui.js',				// JqueryUI (need for tooltip)
                    
					'assets-modules/reports/js/menu.js',
                    'assets-modules/reports/js/live.js',
				],
                'assets-modules/reports/css/vendor.live.css' => [
	                'font-awesome/css/font-awesome.css',
					'ol3/ol.css',
                    'jquery-ui/themes/base/theme.css',	// JqueryUI
                    
                    'assets-modules/reports/css/live.css'
                ],
                
                // Specific Asset for HeatMap Page.
				'assets-modules/reports/js/vendor.map.js' =>[
					'jquery/dist/jquery.js',
                    'jquery.scrollTo/jquery.scrollTo.js',
                    'ol3/ol.js',
                    'seiyria-bootstrap-slider/js/bootstrap-slider.js',
                    'bootstrap-datepicker/dist/js/bootstrap-datepicker.js',                   
                    
					'assets-modules/reports/js/menu.js',
                    'assets-modules/reports/js/map.js',
                    
				],
                'assets-modules/reports/css/vendor.map.css' => [
	                'font-awesome/css/font-awesome.css',
					'ol3/ol.css',
                    'seiyria-bootstrap-slider/dist/css/bootstrap-slider.css',
                    'bootstrap-datepicker/dist/css/bootstrap-datepicker3.css',
                    
                    'assets-modules/reports/css/map.css'
                ],
				
				
            ],
            'paths' => [
                'reports' => __DIR__ . '/../public',
                $baseDir. '/bower_components',
            ],
            'aliases' => [
	            'reports/lib' => $baseDir. '/bower_components/ol2-legacy/lib',
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
