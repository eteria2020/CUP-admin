<?php

namespace Application\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class CsvUploadForm extends Form
{
    /**
     * @param string|null $name
     * @param array  $options
     */
    public function __construct($name = null, array $options = [])
    {
        parent::__construct($name, $options);
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->addElements();
    }

    private function addElements()
    {
        $this->add([
            'name' => 'csv-upload',
            'type' => 'file',
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Carica adesso'
            ],
        ]);
    }
}
