<?php

namespace Application\Form\Validator;

use SharengoCore\Service\CustomersService;
use Zend\Validator\AbstractValidator;

class DuplicateEmail extends AbstractValidator
{
    const DUPLICATE = 'duplicateEmail';

    /**
     * SM
     * @var CustomersService
     */
    private $customersService;

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
        $this->customersService = $options['customerService'];
        if (isset($options['avoid'])) {
            $this->emailsToAvoid = $options['avoid'];
        }
    }

    public function isValid($value)
    {
        $this->setValue($value);

        $customer = $this->customersService->findByEmail($value);

        if (!empty($customer) && !in_array($customer[0]->getEmail(), $this->emailsToAvoid)) {
            $this->error(self::DUPLICATE);
            return false;
        }

        return true;
    }
}
