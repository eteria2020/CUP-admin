<?php
namespace Application\Controller;

use Application\Form\UserForm;
use Application\Form\Validator\DuplicateEmail;
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
    private $usersService;

    /**
     * @var
     */
    private $userForm;

    /** @var DoctrineHydrator */
    private $hydrator;

    /**
     * @param UsersService $usersService
     */
    public function __construct(UsersService $usersService, Form $userForm, DoctrineHydrator $hydrator)
    {
        $this->usersService = $usersService;
        $this->userForm = $userForm;
        $this->hydrator = $hydrator;
    }

    public function indexAction()
    {
        return new ViewModel([
            'users' => $this->usersService->getListUsers()
        ]);
    }

    public function addAction()
    {
        $translator = $this->TranslatorPlugin();
        /** @var UserForm $form */
        $form = $this->userForm;

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);

            $validator = new DuplicateEmail(['service' => $this->usersService]);
            $email = $form->getInputFilter()->get('user')->get('email');
            $email->getValidatorChain()->attach($validator);

            if ($form->isValid()) {

                try {

                    $this->usersService->saveData($form->getData());
                    $this->flashMessenger()->addSuccessMessage($translator->translate('Utente creato con successo!'));

                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage($translator->translate('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente'));

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
        $translator = $this->TranslatorPlugin();
        $id = (int)$this->params()->fromRoute('id', 0);

        /** @var Webuser $I_user */
        $I_user = $this->usersService->findUserById($id);

        if (is_null($I_user)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        /** @var UserForm $form */
        $form = $this->userForm;
        $userData = $this->hydrator->extract($I_user);
        $userData['email2'] = $I_user->getEmail();
        $form->setData(['user' => $userData]);

        if ($this->getRequest()->isPost()) {

            $postData = $this->getRequest()->getPost()->toArray();
            $postData['user']['id'] = $I_user->getId();
            $form->setData($postData);

            $form->getInputFilter()->get('user')->get('password')->setRequired(false);
            $form->getInputFilter()->get('user')->get('password2')->setRequired(false);
            $validator = new DuplicateEmail([
                'service' => $this->usersService,
                'avoid'   => [$I_user->getEmail()]
            ]);
            $email = $form->getInputFilter()->get('user')->get('email');
            $email->getValidatorChain()->attach($validator);

            if ($form->isValid()) {

                try {

                    $this->usersService->saveData($form->getData(), $userData['password']);
                    $this->flashMessenger()->addSuccessMessage($translator->translate('Utente modificato con successo!'));

                } catch (\Exception $e) {

                    $this->flashMessenger()->addErrorMessage($translator->translate('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente'));

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
