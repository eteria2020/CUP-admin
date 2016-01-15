<?php
/**
 * Zend Framework (http://framework.zend.com/).
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 *
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

// Getting the siteroot path ( = sharengo-admin folder)
$baseDir = realpath(__DIR__.'/../../../');

return [
    'router' => [
        'routes' => [
            'reports' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route' => '/reports',
                    'defaults' => [
                        'controller' => 'Reports\Controller\Index',
                        'action' => 'trips',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'trips' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/trips',
                            'defaults' => [
                                'action' => 'trips',
                            ],
                        ],
                    ],
                    'map' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/map',
                            'defaults' => [
                                'action' => 'map',
                            ],
                        ],
                    ],
                    'live' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/live',
                            'defaults' => [
                                'action' => 'live',
                            ],
                        ],
                    ],
                    'routes' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/routes[/:tripid]',
                            'constraints' => [
                                'tripid' => '[0-9]*',
                            ],
                            'defaults' => [
                                'action' => 'routes',
                            ],
                        ],
                    ],
                    'tripscity' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/tripscity/:id',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                            'defaults' => [
                                'action' => 'tripscity',
                            ],
                        ],
                    ],
                    'api' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/api',
                            'defaults' => [
                                'controller' => 'Reports\Controller\Api',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'get-cities' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/get-cities',
                                    'defaults' => [
                                        'action' => 'get-cities',
                                    ],
                                ],
                            ],
                            'get-all-trips' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/get-all-trips',
                                    'defaults' => [
                                        'action' => 'get-all-trips',
                                    ],
                                ],
                            ],
                            'get-city-trips' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/get-city-trips',
                                    'defaults' => [
                                        'action' => 'get-city-trips',
                                    ],
                                ],
                            ],
                            'get-urban-areas' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/get-urban-areas/:city',
                                    'constraints' => [
                                        'city' => '[0-9]*',
                                    ],
                                    'defaults' => [
                                        'action' => 'get-urban-areas',
                                    ],
                                ],
                            ],
                            'get-trips-geo-data' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/get-trips-geo-data',
                                    'defaults' => [
                                        'action' => 'get-trips-geo-data',
                                    ],
                                ],
                            ],
                            'get-trip' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/get-trip/:id',
                                    'constraints' => [
                                        'id' => '[0-9]*',
                                    ],
                                    'defaults' => [
                                        'action' => 'get-trip',
                                    ],
                                ],
                            ],
                            'get-trips' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/get-trips',
                                    'defaults' => [
                                        'action' => 'get-trips',
                                    ],
                                ],
                            ],
                            'get-trips-from-logs' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/get-trips-from-logs',
                                    'defaults' => [
                                        'action' => 'get-trips-from-logs',
                                    ],
                                ],
                            ],
                            'get-trip-points-from-logs' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/get-trip-points-from-logs',
                                    'defaults' => [
                                        'action' => 'get-trip-points-from-logs',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Reports\Controller\Index' => 'Reports\Controller\IndexController',
        ],
        'factories' => [
            'Reports\Controller\Api' => 'Reports\Controller\ApiControllerFactory',
        ],
    ],

    'service_manager' => [
        'factories' => [
            'Reports\Service\Reports' => 'Reports\Service\ReportsServiceFactory',
            'Reports\Service\ReportsCsvService' => 'Reports\Service\ReportsCsvServiceFactory',
            'Reports\Service\Obfuscator' => 'Reports\Service\ObfuscatorFactory',
        ],
    ],

    'asset_manager' => [
        'caching' => [
            'default' => [
                'cache' => 'Assetic\\Cache\\FilesystemCache',
                'options' => [
                    'dir' => 'public/cache', // path/to/cache
                ],
            ],
        ],
        'resolver_configs' => [
            'map' => [
                'assets-modules/reports/js/dc.js.map' => $baseDir.'/bower_components/dcjs/dc.js.map',
                'assets-modules/reports/js/dc.js' => $baseDir.'/bower_components/dcjs/dc.js',
            ],
            'collections' => [
                // Specific Asset for Trips (main] Page.
                'assets-modules/reports/js/vendor.trips.main.js' => [
                    // Libs
                    'jquery/dist/jquery.js',
                    'crossfilter/crossfilter.js',
                    'd3/d3.js',
                    'dcjs/dc.js',
                    // Code
                    'assets-modules/reports/js/menu.js',
                    'assets-modules/reports/js/trips.main.js',
                ],
                'assets-modules/reports/css/vendor.trips.main.css' => [
                    // Libs
                    'font-awesome/css/font-awesome.css',
                    'dcjs/dc.css',

                    // Code
                    'assets-modules/reports/css/trips.main.css',
                ],

                // Specific Asset for Trips (city] Page.
                'assets-modules/reports/js/vendor.trips.city.js' => [
                    // Libs
                    'jquery/dist/jquery.js',
                    'crossfilter/crossfilter.js',
                    'd3/d3.js',
                    'dcjs/dc.js',

                     // Code
                    'assets-modules/reports/js/menu.js',
                    'assets-modules/reports/js/trips.city.js',
                ],
                'assets-modules/reports/css/vendor.trips.city.css' => [
                    // Getting the trips.main libraries
                    'assets-modules/reports/css/vendor.trips.main.css',
                ],

                // Specific Asset for Live Page.
                'assets-modules/reports/js/vendor.live.js' => [
                    // Libs
                    'jquery/dist/jquery.js',
                    'jquery-scrollto/jquery.scrollTo.js',
                    'ol3/ol.js',
                    'jquery-ui/jquery-ui.js',                // JqueryUI (need for tooltip]

                    // Code
                    'assets-modules/reports/js/menu.js',
                    'assets-modules/reports/js/live.js',
                ],
                'assets-modules/reports/css/vendor.live.css' => [
                    // Libs
                    'font-awesome/css/font-awesome.css',
                    'ol3/ol.css',
                    'jquery-ui/themes/base/theme.css',        // JqueryUI

                    // Code
                    'assets-modules/reports/css/live.css',
                ],

                // Specific Asset for Map Page.
                'assets-modules/reports/js/vendor.map.js' => [
                    // Libs
                    'jquery/dist/jquery.js',
                    'jquery-scrollto/jquery.scrollTo.js',
                    'ol3/ol.js',
                    'seiyria-bootstrap-slider/dist/bootstrap-slider.js',
                    'bootstrap-datepicker/dist/js/bootstrap-datepicker.js',

                    // Code
                    'assets-modules/reports/js/menu.js',
                    'assets-modules/reports/js/map.js',
                ],
                'assets-modules/reports/css/vendor.map.css' => [
                    // Libs
                    'font-awesome/css/font-awesome.css',
                    'ol3/ol.css',
                    'seiyria-bootstrap-slider/dist/css/bootstrap-slider.css',
                    'bootstrap-datepicker/dist/css/bootstrap-datepicker3.css',

                    // Code
                    'assets-modules/reports/css/map.css',
                ],

                // Specific Asset for Routes Page.
                'assets-modules/reports/js/vendor.routes.js' => [
                    // Libs
                    'ol3/ol.js',                                // OpenLayers3
                    //'ol3/ol-debug.js',                        // OpenLayers3 Debug
                    'jquery-legacy/dist/jquery.js',             // Jquery 1.11.3
                    'jquery-migrate/jquery-migrate.js',         // Jquery Migrate 1.2.1
                    'jquery-scrollto/jquery.scrollTo.js',       // ScrollTo
                    'moment/moment.js',                         // Moment.js
                    'eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
                    'seiyria-bootstrap-slider/dist/bootstrap-slider.js',
                    'bootstrap-switch/dist/js/bootstrap-switch.js',

                    // Code
                    'assets-modules/reports/js/menu.js',
                    'assets-modules/reports/js/routes.js',
                ],
                'assets-modules/reports/css/vendor.routes.css' => [
                    // Libs
                    'eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css',
                    'seiyria-bootstrap-slider/dist/css/bootstrap-slider.css',
                    'bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css',
                    'ol3/ol.css',

                    // Code
                    'assets-modules/reports/css/routes.css',
                ],
            ],
            'paths' => [
                'reports' => __DIR__.'/../public',
                $baseDir.'/bower_components',
            ],
            'aliases' => [
                'reports/lib' => $baseDir.'/bower_components/ol2-legacy/lib',
            ],
        ],
        'filters' => [
            // Obfuscate only specific files to prevent libs error
            'assets-modules/reports/js/routes.js' => [
                [
                    'service' => 'Reports\Service\Obfuscator', //'obfuscator',
                ],
            ],
            'assets-modules/reports/js/menu.js' => [
                [
                    'service' => 'Reports\Service\Obfuscator', //'obfuscator',
                ],
            ],
            'assets-modules/reports/js/trips.main.js' => [
                [
                    'service' => 'Reports\Service\Obfuscator', //'obfuscator',
                ],
            ],
            'assets-modules/reports/js/trips.city.js' => [
                [
                    'service' => 'Reports\Service\Obfuscator', //'obfuscator',
                ],
            ],
            'assets-modules/reports/js/live.js' => [
                [
                    'service' => 'Reports\Service\Obfuscator', //'obfuscator',
                ],
            ],
            'assets-modules/reports/js/map.js' => [
                [
                    'service' => 'Reports\Service\Obfuscator', //'obfuscator',
                ],
            ],

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
    'view_manager' => [
        'template_map' => [
            'layout/reports' => __DIR__.'/../view/layout/layout.phtml',
        ],
        'template_path_stack' => [
            __DIR__.'/../view',
        ],
    ],
    // ACL
    'bjyauthorize' => [
        'guards' => [
            'BjyAuthorize\Guard\Controller' => [
                ['controller' => 'Reports\Controller\Index', 'roles' => ['admin', 'callcenter']],
                ['controller' => 'Reports\Controller\Api', 'roles' => ['admin', 'callcenter']],
            ],
        ],
    ],
];
