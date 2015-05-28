<?php

namespace Application\Form;

use SharengoCore\Entity\Customers;
use Zend\Form\Form;
use Doctrine\ORM\EntityManager;

class UserForm extends Form
{
    /**
     * @var \Doctrine\ORM\EntityManager;
     */
    private $entityManager;

    public function __construct($userFieldset, EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

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
        $user = $this->getData();
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
