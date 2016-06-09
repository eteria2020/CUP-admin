<?php

namespace Application\Form;

// Internals
use Application\Form\ZoneFieldset;
use SharengoCore\Entity\Zone;
// Externals
use Zend\Form\Form;
use Doctrine\ORM\EntityManager;

class ZoneForm extends Form
{
    /**
     * @param ZoneFieldset $zoneFieldset
     * @param EntityManager $entityManager
     */
    public function __construct(ZoneFieldset $zoneFieldset, EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct('zone');
        $this->setAttribute('method', 'post');

        $this->add($zoneFieldset);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Submit'
            ]
        ]);
    }
}
