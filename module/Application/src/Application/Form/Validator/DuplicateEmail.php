<?php

namespace Application\Form\Validator;

use SharengoCore\Service\ServiceInterface;
use Zend\Validator\AbstractValidator;

class DuplicateEmail extends AbstractValidator
{
    const DUPLICATE = 'duplicateEmail';

    /** @var  ServiceInterface */
    private $service;

    /**
     * @var array
     */
    private $emailsToAvoid = [];

    protected $messageTemplates = [
        self::DUPLICATE => "Esiste già un utente con lo stesso indirizzo email"
    ];

    public function __construct($options)
    {
        parent::__construct();

        $this->service = $options['service'];

        if (isset($options['avoid'])) {
            $this->emailsToAvoid = $options['avoid'];
        }
    }

    public function isValid($value)
    {
        $this->setValue($value);

        $data = $this->service->findByEmail($value);

        if (!empty($data) && !in_array($data[0]->getEmail(), $this->emailsToAvoid)) {
            $this->error(self::DUPLICATE);
            return false;
        }

        return true;
    }
}
