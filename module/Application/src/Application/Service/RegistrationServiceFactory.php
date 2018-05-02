<?php

namespace Application\Service;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Mail\Transport\Sendmail;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RegistrationServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $emailSettings = $serviceLocator->get('Configuration')['emailSettings'];
        $emailService = $serviceLocator->get('SharengoCore\Service\EmailService');
        $translationService = $serviceLocator->get('Translator');
        $viewHelperManager = $serviceLocator->get('viewHelperManager');
        $config = $serviceLocator->getServiceLocator()->get('Config');
        $website = $config['website'];


        return new RegistrationService(
            $entityManager,
            $emailSettings,
            $emailService,
            $translationService,
            $viewHelperManager,
            $website
        );
    }
}
