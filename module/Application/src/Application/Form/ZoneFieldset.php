<?php
namespace Application\Form;

// Internals
use SharengoCore\Entity\Zone;
use SharengoCore\Service\ZonesService;
use SharengoCore\Service\PostGisService;
// Externals
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Mvc\I18n\Translator;

/**
 * Class ZoneFieldset
 * @package Application\Form
 */
class ZoneFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @var ZoneService
     */
    private $zonesService;

    /**
     * @var PostGisService
     */
    private $postGisService;

    /**
     * @param ZonesService $zonesService
     * @param PostGisService $postGisService
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        ZonesService $zonesService,
        PostGisService $postGisService,
        HydratorInterface $hydrator,
        Translator $translator
    ) {
        $this->zonesService = $zonesService;
        $this->postGisService = $postGisService;

        $this->setHydrator($hydrator);
        $this->setObject(new Zone());

        parent::__construct('zone', [
            'use_as_base_fieldset' => true
        ]);

        $this->add([
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => [
                'id' => 'id'
            ],
        ]);

        $this->add([
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'name',
                'class' => 'form-control'
            ],
        ]);

        $this->add([
            'name' => 'active',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'active',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                    1 => $translator->translate("Attivo"),
                    0 => $translator->translate("Non Attivo")
                ],
            ],
        ]);

        $this->add([
            'name' => 'hidden',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'hidden',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                    1 => $translator->translate("Nascosta"),
                    0 => $translator->translate("Visibile")
                ],
            ],
        ]);

        $this->add([
            'name' => 'invoiceDescription',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'invoice-description',
                'class' => 'form-control'
            ],
        ]);

        $this->add([
            'name' => 'revGeo',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'revGeo',
                'class' => 'form-control',
            ],
            'options' => [
                'value_options' => [
                    1 => $translator->translate("Usa RevGeo"),
                    0 => $translator->translate("Non Usa RevGeo")
                ],
            ],
        ]);

        $this->add([
            'name' => 'useKmlFile',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'useKmlFile',
                'class' => 'form-control',
                'required' => 'required'
            ],
            'options' => [
                'value_options' => [
                    0 => $translator->translate("Usa String GeoJSON"),
                    1 => $translator->translate("Usa File KML")
                ],
            ],
        ])->setValue(0);

        $this->add([
            'name' => 'kmlUpload',
            'type' => 'file',
            'validators' => [
                [
                    'name' => 'File\MimeType',
                    'options' => [
                        'mimeType' => 'text/xml'
                    ],
                ],
            ],
        ]);

        $this->add([
            'name' => 'areaUse',
            'type' => 'Application\Form\Element\GeometryTextarea',
            'attributes' => [
                'id' => 'areaUse',
                'class' => 'form-control'
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'active' => [
                'required' => true,
            ],
            'name' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ],
                ],
                'validators' => [
                    [
                        'name' =>'NotEmpty'
                    ],
                ],
            ],
            'hidden' => [
                'required' => true,
            ],
            'invoiceDescription' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ],
                ],
                'validators' => [
                    [
                        'name' =>'NotEmpty',
                    ],
                ],
            ],
            'revGeo' => [
                'required' => true,
            ],
            'kmlUpload' => [
                'required' => false,
            ],
            'areaUse' => [
                'required' => false,
            ],
        ];
    }
}
