<?php

namespace Application\Form\Validator;

use SharengoCore\Service\CustomersService;
use SharengoCore\Service\UsersService;
use Zend\Validator\AbstractValidator;

class DuplicateEmail extends AbstractValidator
{
    const DUPLICATE = 'duplicateEmail';

    /**
     * SM
     * @var CustomersService
     */
    private $customersService = null;

    /**
     * SM
     * @var UsersService
     */
    private $userService = null;

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

        if(isset($options['customerService'])) {
            $this->customersService = $options['customerService'];
        }

        if(isset($options['userService'])) {
            $this->userService = $options['userService'];
        }

        if (isset($options['avoid'])) {
            $this->emailsToAvoid = $options['avoid'];
        }
    }

    public function isValid($value)
    {
        $this->setValue($value);

        if(!is_null($this->customersService)) {
            $data = $this->customersService->findByEmail($value);
        }

        if(!is_null($this->userService)) {
            $data = $this->userService->findByEmail($value);
        }

        if (!empty($data) && !in_array($data[0]->getEmail(), $this->emailsToAvoid)) {
            $this->error(self::DUPLICATE);
            return false;
        }

        return true;
    }
}
