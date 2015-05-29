<?php

namespace Application\Form;

use SharengoCore\Entity\Customers;
use Zend\Crypt\Password\Bcrypt;
use Zend\Form\Form;
use Doctrine\ORM\EntityManager;
use ZfcUser\Options\UserServiceOptionsInterface;
use ZfcUserDoctrineORM\Entity\User;

class UserForm extends Form
{
    /**
     * @var \Doctrine\ORM\EntityManager;
     */
    private $entityManager;

    /**
     * @var UserServiceOptionsInterface
     */
    protected $options;

    public function __construct($userFieldset, EntityManager $entityManager, UserServiceOptionsInterface $options)
    {
        $this->entityManager = $entityManager;
        $this->options = $options;

        parent::__construct('user');
        $this->setAttribute('method', 'post');

        $this->add($userFieldset);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Submit'
            ]
        ]);
    }

    /**
     * persists the form data in the database and returns the saved data
     *
     * @return Customers
     */
    public function saveData()
    {
        /** @var User $user */
        $user = $this->getData();
        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->options->getPasswordCost());
        $user->setPassword($bcrypt->create($user->getPassword()));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
