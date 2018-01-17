<?php
namespace Application\Controller;

// Internals
use SharengoCore\Entity\Notifications;
use SharengoCore\Service\NotificationsService;
use SharengoCore\Service\NotificationsProtocolsService;
use SharengoCore\Service\NotificationsCategoriesService;
use SharengoCore\Service\NotificationsCategories\NotificationsCategoriesAbstractFactory;
// Externals
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Doctrine\DBAL\Exception\DriverException;
use Zend\Http\Response;

class NotificationsController extends AbstractActionController
{
    /**
     * @var NotificationsService
     */
    private $notificationsService;

    /**
     * @var NotificationsCategoriesService
     */
    private $notificationsCategories;

    /**
     * @var NotificationsProtocolsService
     */
    private $notificationsProtocols;

    /**
     * @var NotificationsCategoriesAbstractFactory
     */
    private $notificationsCategoriesAbstractFactory;

    /**
     * @var Container
     */
    private $datatableFiltersSessionContainer;
    
    //variabile sessione
    private $sessionAllarm;

    /**
     * @param NotificationsService $notificationsService
     * @param NotificationsProtocolsService $notificationsProtocols
     * @param NotificationsCategoriesService $notificationsCategories
     * @param NotificationsCategoriesAbstractFactory $notificationsCategoriesAbstractFactory
     * @param Container $datatableFiltersSessionContainer
     */
    public function __construct(
        NotificationsService $notificationsService,
        NotificationsProtocolsService $notificationsProtocols,
        NotificationsCategoriesService $notificationsCategories,
        NotificationsCategoriesAbstractFactory $notificationsCategoriesAbstractFactory,
        Container $datatableFiltersSessionContainer
    ) {
        $this->notificationsService = $notificationsService;
        $this->notificationsProtocols = $notificationsProtocols;
        $this->notificationsCategories = $notificationsCategories;
        $this->notificationsCategoriesAbstractFactory = $notificationsCategoriesAbstractFactory;
        $this->datatableFiltersSessionContainer = $datatableFiltersSessionContainer;
    }

    /**
     * This method return an array containing the DataTable filters,
     * from a Session Container.
     *
     * @return array
     */
    private function getDataTableSessionFilters()
    {
        return $this->datatableFiltersSessionContainer->offsetGet('Notifications');
    }

    public function indexAction()
    {
        $sessionDatatableFilters = $this->getDataTableSessionFilters();

        return new ViewModel([
            'filters' => json_encode($sessionDatatableFilters),
            'notificationsCategories' => $this->notificationsCategories->getListNotificationsCategories(),
            'notificationsProtocols' => $this->notificationsProtocols->getListNotificationsProtocols()
        ]);
    }

    /**
     * This method print the detail page for a given Notifications id number.
     *
     * @return ViewModel
     */
    public function detailsAction()
    {
        $translator = $this->TranslatorPlugin();

        // Get the Notification ID from route
        $id = (int) $this->params()->fromRoute('id', 0);

        try {
            // Get the notification Object
            $notification = $this->notificationsService->getNotificationById($id);
        } catch (DriverException $e) {
            $this->flashMessenger()->addErrorMessage($translator->translate('Il valore identificativo della notifica non è un valore accettato.'));
        }

        if (!$notification instanceof Notifications) {
            return $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
        }

        try {
            // Get the notification Class Service
            $categoryService = $this->notificationsCategoriesAbstractFactory->createServiceWithName(
                'SharengoCore\\Service\\NotificationsCategories\\' . strtoupper($notification->getCategoryNameSlug()) . 'CategoryService'
            );
        } catch (NotificationsServiceNotFoundException $e) {
            $this->flashMessenger()->addErrorMessage($translator->translate('Il valore identificativo della notifica non è un valore accettato.'));
        }

        return new ViewModel([
            'notification' => $notification,
            'data' => $categoryService->getData($notification)
        ]);
    }

    public function datatableAction()
    {
        $filters = $this->params()->fromPost();
        $filters['withLimit'] = true;
        $dataDataTable = $this->notificationsService->getDataDataTable($filters);
        $totalNotifications = $this->notificationsService->getTotalNotifications();
        $recordsFiltered = $this->getRecordsFiltered($filters, $totalNotifications);
        
        $lastId = current((Array)current((Array)$dataDataTable)[0])->id;
        
        $sessionAllarm = new Container('sessionAllarm');
        if(!$sessionAllarm->offsetExists('maxId')){
            $sessionAllarm->offsetSet('maxId', $lastId);
            $checkAllarm = false;
        }else{
            if($sessionAllarm->offsetGet('maxId') < $lastId){
                $sessionAllarm->offsetSet('maxId', $lastId);
                $checkAllarm = true;
            }else{
                $checkAllarm = false;
            }
        }
        
        return new JsonModel([
            'draw' => $this->params()->fromQuery('sEcho', 0),
            'checkAllarm' => $checkAllarm,
            'recordsTotal' => $totalNotifications,
            'recordsFiltered' => $recordsFiltered,
            'data' => $dataDataTable
        ]);
    }

    /**
     * Sets the acknowledge to the actual datetime of a specified notification.
     * The notification is retrived with the route parameter "id".
     *
     * @return JsonModel ([ 'data' => DateTime | null ])
     */
    public function ajaxAcknowledgmentAction()
    {
        $translator = $this->TranslatorPlugin();

        // Get the Notification ID from route
        $id = (int) $this->params()->fromRoute('id', 0);

        $date = null;

        try {
            // Get the notification Object
            $notification = $this->notificationsService->getNotificationById($id);

            // Create the $acknolageDate
            $acknolageDate = date_create();

            // Make the acknowledgment
            $this->notificationsService->acknowledge($notification, $acknolageDate);

            $this->flashMessenger()->addSuccessMessage($translator->translate('Presa visione della notifica con ID: ') . ' ' . $id . '.');
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage($translator->translate('Errore durante la presa visione della notifica.'));
        }

        return new JsonModel([
            'dateTimeZ' => $acknolageDate,
            'dateTimeStamp' => $acknolageDate->getTimestamp()
        ]);
    }

    protected function getRecordsFiltered($filters, $totalNotifications)
    {
        if (empty($filters['searchValue']) && !isset($filters['columnValueWithoutLike'])) {
            return $totalNotifications;
        } else {
            $filters['withLimit'] = false;
            return $this->notificationsService->getDataDataTable($filters, true);
        }
    }
}
