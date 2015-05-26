<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class DuplicateTaxCode extends AbstractValidator
{
    const DUPLICATE = 'duplicateTaxCode';

    /**
     * SM
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    private $customersService;

    /**
     * @var array
     */
    private $taxCodesToAvoid = [];

    protected $messageTemplates = [
        self::DUPLICATE => "Esiste già un utente con lo stesso codice fiscale"
    ];

    public function __construct($options)
    {
        parent::__construct();
        $this->customersService = $options['customerService'];
        if (isset($options['avoid'])) {
            $this->taxCodesToAvoid = $options['avoid'];
        }
    }

    public function isValid($value)
    {
        $this->setValue($value);

        $customer = $this->customersService->findByTaxCode(strtoupper($value));

        if (!empty($customer)  && !in_array($customer[0]->getTaxCode(), $this->taxCodesToAvoid)) {
            $this->error(self::DUPLICATE);
            return false;
        }

        return true;
    }
}
