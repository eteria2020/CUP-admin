<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class DuplicateCards extends AbstractValidator
{
    const DUPLICATE = 'duplicateCard';

    private $service;

    protected $messageTemplates = [
        self::DUPLICATE => "Questo codice esiste già"
    ];

    public function __construct($options)
    {
        parent::__construct();

        $this->service = $options['service'];
    }

    public function isValid($value)
    {
        $this->setValue($value);

        $data = $this->service->getCard($value);

        if (!empty($data)) {
            $this->error(self::DUPLICATE);

            return false;
        }

        return true;
    }
}
