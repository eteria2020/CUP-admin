<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace EventLogger;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use BjyAuthorize\View\RedirectionStrategy;
use Doctrine\ORM\Mapping\Driver\XmlDriver;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $application = $e->getApplication();
        $serviceManager = $application->getServiceManager();


        // attach event logger listener (only if a user is logged)
        $auth = $serviceManager->get('zfcuser_auth_service');
        if ($auth->hasIdentity()) {
            $sharedEventManager = $application->getEventManager()->getSharedManager();
            $sharedEventManager->attachAggregate($serviceManager->get('EventLogger\Listener\UserEventListener'));
        }
        
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }
}
