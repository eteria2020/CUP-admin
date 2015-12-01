<?php

namespace Application\Form;

use SharengoCore\Entity\Configurations;
use SharengoCore\Service\ConfigurationsService;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Class ConfigurationsForm
 * @package Application\Form
 */
class ConfigurationsForm extends Form
{
    /**
     * @var ConfigurationsService
     */
    private $configurationsService;

    /**
     * ConfigurationsForm constructor.
     *
     * @param ConfigurationsService $configurationsService
     * @param HydratorInterface     $hydrato
     */
    public function __construct(
        ConfigurationsService $configurationsService,
        HydratorInterface $hydrato
    ) {
        $this->configurationsService = $configurationsService;

        parent::__construct('configurations');
        $this->setAttribute('method', 'post');
        $configurationsFieldset = new ConfigurationsFieldset($hydrato);

        $this->add([
            'type'    => 'Zend\Form\Element\Collection',
            'name'    => 'configurations',
            'options' => [
                'count'          => count($this->configurationsService->getConfigurationsBySlug(Configurations::ALARM)),
                'target_element' => $configurationsFieldset,
                'use_as_base_fieldset' => true,
                'should_create_template' => true
            ]
        ]);
    }
}
