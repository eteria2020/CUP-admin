<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class EighteenDate extends AbstractValidator
{
    const EIGHTEEN = 'eighteenDate';

    protected $messageTemplates = [
        self::EIGHTEEN => "E' necessario essere maggiorenni"
    ];

    public function isValid($value)
    {
        $this->setValue($value);

        $date = date_create($value);
        $eighteen = date_create("-18 years");

        if ($date > $eighteen) {
            $this->error(self::EIGHTEEN);
            return false;
        }

        return true;
    }
}
