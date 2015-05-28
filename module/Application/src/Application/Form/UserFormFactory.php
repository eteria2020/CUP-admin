<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class UserFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return UserForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $I_usersService = $serviceLocator->get('SharengoCore\Service\UsersService');

        $hydrator = new DoctrineHydrator($entityManager);
        $userFieldset = new UserFieldset($I_usersService, $hydrator);

        return new UserForm($userFieldset, $entityManager);
    }
}
