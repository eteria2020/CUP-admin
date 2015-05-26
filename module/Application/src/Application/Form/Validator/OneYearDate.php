<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class OneYearDate extends AbstractValidator
{
    const ONEYEAR = 'oneYearDate';

    protected $messageTemplates = [
        self::ONEYEAR => "E' necessario avere la patente da almeno un anno"
    ];

    public function isValid($value)
    {
        $this->setValue($value);

        $date = date_create($value);
        $oneYear = date_create("-1 years");

        if ($date > $oneYear) {
            $this->error(self::ONEYEAR);
            return false;
        }

        return true;
    }
}
