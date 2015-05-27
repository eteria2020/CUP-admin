<?php
namespace Application\Controller;

use SharengoCore\Service\UsersService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UsersController extends AbstractActionController
{
    /**
     * @var UsersService
     */
    private $I_usersService;

    /**
     * @param UsersService $I_usersService
     */
    public function __construct(UsersService $I_usersService)
    {
        $this->I_usersService = $I_usersService;
    }

    public function indexAction()
    {
        return new ViewModel([
            'users' => $this->I_usersService->getListUsers()
        ]);
    }

    public function addAction()
    {
        return new ViewModel([]);
    }
}
