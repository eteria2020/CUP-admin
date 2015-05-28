<?php
namespace Application\Controller;

use Application\Form\UserForm;
use SharengoCore\Service\UsersService;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UsersController extends AbstractActionController
{
    /**
     * @var UsersService
     */
    private $I_usersService;

    /**
     * @var
     */
    private $I_userForm;

    /**
     * @param UsersService $I_usersService
     */
    public function __construct(UsersService $I_usersService, Form $I_userForm)
    {
        $this->I_usersService = $I_usersService;
        $this->I_userForm = $I_userForm;
    }

    public function indexAction()
    {
        return new ViewModel([
            'users' => $this->I_usersService->getListUsers()
        ]);
    }

    public function addAction()
    {
        /** @var UserForm $form */
        $form = $this->I_userForm;

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);


            if ($form->isValid()) {

                try {

                    $form->saveData();
                    $this->flashMessenger()->addSuccessMessage('Utente creato con successo!');

                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage($e->getMessage());

                }

                return $this->redirect()->toRoute('users');
            }
        }

        return new ViewModel([
            'userForm' => $form
        ]);
    }
}
