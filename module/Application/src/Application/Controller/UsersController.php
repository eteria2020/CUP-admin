<?php
namespace Application\Controller;

use Application\Entity\Webuser;
use Application\Form\UserForm;
use SharengoCore\Service\UsersService;
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;


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

    /** @var DoctrineHydrator */
    private $hydrator;

    /**
     * @param UsersService $I_usersService
     */
    public function __construct(UsersService $I_usersService, Form $I_userForm, DoctrineHydrator $hydrator)
    {
        $this->I_usersService = $I_usersService;
        $this->I_userForm = $I_userForm;
        $this->hydrator = $hydrator;
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

                    $this->I_usersService->saveData($form->getData());
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

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        /** @var Webuser $I_user */
        $I_user = $this->I_usersService->findUserById($id);

        if (is_null($I_user)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        /** @var UserForm $form */
        $form = $this->I_userForm;
        $userData = $this->hydrator->extract($I_user);
        $userData['email2'] = $I_user->getEmail();
        $form->setData(['user' => $userData]);

        if ($this->getRequest()->isPost()) {

            $postData = $this->getRequest()->getPost()->toArray();
            $postData['user']['id'] = $I_user->getId();
            $form->setData($postData);

            $this->I_usersService->setEditMode(true);
            $this->I_usersService->setValidatorEmail($I_user->getEmail());

            if ($form->isValid()) {

                try {

                    $this->I_usersService->saveData($form->getData(), $userData['password']);
                    $this->flashMessenger()->addSuccessMessage('Utente modificato con successo!');

                } catch (\Exception $e) {

                    $this->flashMessenger()->addErrorMessage($e->getMessage());

                }

                return $this->redirect()->toRoute('users');
            }
        }

        return new ViewModel([
            'userForm' => $form,
            'user'     => $I_user
        ]);
    }
}
