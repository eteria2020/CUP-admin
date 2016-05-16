<?php

namespace Application\Form;

use Zend\Form\Form;
use Doctrine\ORM\EntityManager;

class ZoneForm extends Form
{
    public function __construct($zoneFieldset, EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct('zone');
        $this->setAttribute('method', 'post');

        $this->add($zoneFieldset);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Submit'
            ]
        ]);
    }
}
