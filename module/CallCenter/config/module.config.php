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
            'call-center' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/call-center',
                    'defaults' => array(
                        'controller' => 'CallCenter\Controller\Index',
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
            'CallCenter\Controller\Index' => 'CallCenter\Controller\IndexControllerFactory'
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
                'assets-modules/call-center/css/vendors.css' => array(
                    'bootstrap/dist/css/bootstrap.min.css'
                ),
                'assets-modules/call-center/css/style.css' => array(
                    'assets-modules/call-center/css/style.less'
                ),
                'assets-modules/call-center/js/vendors.js' => array(
                    'jquery/dist/jquery.js',
                    'angular/angular.js',
                    'bootstrap/dist/js/bootstrap.js',
                    'lodash/dist/lodash.min.js',
                    'angular-google-maps/dist/angular-google-maps.min.js',
                    'angular-bootstrap/ui-bootstrap-tpls.min.js',
                    'moment/min/moment.min.js',
                ),
                'assets-modules/call-center/js/scripts.js' => array(
                    'assets-modules/call-center/js/app.js',
                    'assets-modules/call-center/js/config/config.js',
                    'assets-modules/call-center/js/config/gmapconfig.js',
                    'assets-modules/call-center/js/factories/carsFactory.js',
                    'assets-modules/call-center/js/factories/usersFactory.js',
                    //'assets-modules/call-center/js/factories/mapFactory.js',
                    'assets-modules/call-center/js/factories/ticketsFactory.js',
                    'assets-modules/call-center/js/directives/angular-reverse-geocode.js',
                    'assets-modules/call-center/js/filter/dateSharengoFormat.js',
                    'assets-modules/call-center/js/controllers/main.js',
                )
            ),
            'map' => array(
                'assets-modules/call-center/fonts/glyphicons-halflings-regular.woff2' =>  $baseDir. '/bower_components/bootstrap/dist/fonts/glyphicons-halflings-regular.woff2'
            ),
            'paths' => array(
                'call-center' => __DIR__ . '/../public',
                $baseDir. '/bower_components',
            ),
        ),
        'filters' => array(
            'assets-modules/call-center/css/style.css' => array(
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

                array('controller' => 'CallCenter\Controller\Index', 'roles' => array('admin'))

            ),
        ),
    ),
);
