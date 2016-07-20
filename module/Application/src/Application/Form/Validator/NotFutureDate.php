<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class NotFutureDate extends AbstractValidator
{
    const NOT_FUTURE = 'oneYearDate';

    protected $messageTemplates = [
        self::NOT_FUTURE => "La data della patente non può essere nel futuro"
    ];

    public function isValid($value)
    {
        $this->setValue($value);

        $date = date_create($value);

        if ($date > date_create()) {
            $this->error(self::NOT_FUTURE);
            return false;
        }

        return true;
    }
}
