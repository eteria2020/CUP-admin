<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

$baseDir = realpath(__DIR__ . '/../../../');

return array(
    'router' => array(
        'routes' => array(
            'alarms' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/alarms',
                    'defaults' => array(
                        'controller' => 'Alarms\Controller\Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        )
    ),

    'controllers' => array(
        'invokables' => array(),

        'factories' => array(
            'Alarms\Controller\Index' => 'Alarms\Controller\IndexControllerFactory'
        )
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        )
    ),

    'asset_manager' => array(
        'resolver_configs' => array(
            'collections' => array(
                'assets-modules/alarms/css/vendors.css' => array(
                    'bootstrap/dist/css/bootstrap.min.css'
                ),
                'assets-modules/alarms/css/style.css' => array(
                    'assets-modules/alarms/css/style.less'
                ),
                'assets-modules/alarms/js/vendors.js' => array(
                    'jquery/dist/jquery.js',
                    'angular/angular.js',
                    'bootstrap/dist/js/bootstrap.js',
                    'lodash/dist/lodash.min.js',
                    'angular-google-maps/dist/angular-google-maps.min.js',
                    'angular-bootstrap/ui-bootstrap-tpls.min.js',
                    'moment/min/moment.min.js',
                ),
                'assets-modules/alarms/js/scripts.js' => array(
                    'assets-modules/alarms/js/app.js',
                    'assets-modules/alarms/js/config/config.js',
                    'assets-modules/alarms/js/config/gmapconfig.js',
                    'assets-modules/alarms/js/factories/carsFactory.js',
                    'assets-modules/alarms/js/factories/usersFactory.js',
                    'assets-modules/alarms/js/factories/poisFactory.js',
                    'assets-modules/alarms/js/factories/ticketsFactory.js',
                    'assets-modules/alarms/js/factories/fleetsFactory.js',
                    'assets-modules/alarms/js/directives/angular-reverse-geocode.js',
                    'assets-modules/alarms/js/filter/dateSharengoFormat.js',
                    'assets-modules/alarms/js/controllers/main.js',
                )
            ),
            'map' => array(
                'assets-modules/alarms/fonts/glyphicons-halflings-regular.woff2' =>  $baseDir. '/bower_components/bootstrap/dist/fonts/glyphicons-halflings-regular.woff2'
            ),
            'paths' => array(
                'alarms' => __DIR__ . '/../public',
                $baseDir. '/bower_components',
            ),
        ),
        'filters' => array(
            'assets-modules/alarms/css/style.css' => array(
                array(
                    'filter' => 'Lessphp',
                ),
            )
        ),
    ),

    // ACL
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(

                array('controller' => 'Alarms\Controller\Index', 'roles' => array('admin','callcenter'))

            ),
        ),
    ),
);
