<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Prompt\Password;
use ZfcUser\Service\User as UserService;

class ConsoleUserController extends AbstractActionController
{
    /**
     * @var UserService
     */
    private $userService;

    public function __construct($userService)
    {
        $this->userService = $userService;
    }

    public function registerAction()
    {
        $email = $this->getRequest()->getParam('email');

        if (empty($email)) {
            echo "please provide an email address after 'register'\n";
            exit;
        }

        $password = Password::prompt('please choose a password');
        $passwordVerify = Password::prompt('please confirm the password');

        $data = [
            'email' => $email,
            'password' => $password,
            'passwordVerify' => $passwordVerify
        ];

        $user = $this->userService->register($data);

        if (!$user) {
            echo "there was an error. Please verify your data\n";
        } else {
            echo "user added. It can now access the system\n";
        }

        exit;
    }
}
