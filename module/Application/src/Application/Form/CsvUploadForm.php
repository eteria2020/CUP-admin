<?php

namespace Application\Form;

use Application\Controller\Plugin\TranslatorPlugin;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Validator\File\MimeType;
use Zend\Validator\File\Extension;

class CsvUploadForm extends Form
{
    /**
     * @param string|null $name
     * @param array  $options
     */

    private $translator;
    public function __construct($translator,$name = null, array $options = [])
    {
        parent::__construct($name, $options);
        $this->translator = $translator;
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->addElements();
        $this->addInputFilter();
    }

    private function addElements()
    {
        $this->add([
            'name' => 'csv-upload',
            'type' => 'file',
            'validators' => [
                [
                    'name' => 'File\MimeType',
                    'options' => [
                        'mimeType' => 'text/csv'
                    ]
                ]
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => $this->translator->translate('Carica adesso'),
                'class' => 'btn green'
            ],
        ]);
    }

    private function addInputFilter()
    {
        $inputFilter = new InputFilter();
        $inputFactory = new InputFactory();

        $inputFilter->add(
            $inputFactory->createInput([
                'name' => 'csv-upload',
                'validators' => [
                    [
                        'name' => 'File/MimeType',
                        'options' => [
                            'mimeType' => 'text/csv,text/plain',
                            'messages' => [
                                MimeType::FALSE_TYPE => $this->translator->translate('Il file caricato ha un formato non valido; sono accettati solo file in formato csv'),
                                MimeType::NOT_DETECTED => $this->translator->translate('Non è stato possibile verificare il formato del file'),
                                MimeType::NOT_READABLE => $this->translator->translate('Il file caricato non è leggibile o non esiste')
                            ]
                        ],
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'File/Extension',
                        'options' => [
                            'extension' => 'csv',
                            'messages' => [
                                Extension::FALSE_EXTENSION => $this->translator->translate('Il file caricato ha un formato non valido; sono accettati solo file in formato csv'),
                                Extension::NOT_FOUND => $this->translator->translate('Il file caricato non è leggibile o non esiste')
                            ]
                        ]
                    ]
                ]
            ])
        );

        $this->setInputFilter($inputFilter);
    }
}
