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
        
        $a = "{\"data\":[{\"e\":{\"id\":52969,\"subject\":\"SOS call\",\"submitDate\":1515440451,\"sentDate\":1515440459,\"acknowledgeDate\":null},\"nc\":{\"name\":\"SOS\"},\"np\":{\"name\":\"Telegram\"},\"button\":52969},{\"e\":{\"id\":52968,\"subject\":\"SOS call\",\"submitDate\":1515440365,\"sentDate\":1515440369,\"acknowledgeDate\":null},\"nc\":{\"name\":\"SOS\"},\"np\":{\"name\":\"Telegram\"},\"button\":52968},{\"e\":{\"id\":52967,\"subject\":\"SOS call\",\"submitDate\":1515439288,\"sentDate\":1515439299,\"acknowledgeDate\":null},\"nc\":{\"name\":\"SOS\"},\"np\":{\"name\":\"Telegram\"},\"button\":52967},{\"e\":{\"id\":52966,\"subject\":\"SOS call\",\"submitDate\":1515439255,\"sentDate\":1515439259,\"acknowledgeDate\":null},\"nc\":{\"name\":\"SOS\"},\"np\":{\"name\":\"Telegram\"},\"button\":52966},{\"e\":{\"id\":52965,\"subject\":\"SOS call\",\"submitDate\":1515438502,\"sentDate\":1515438509,\"acknowledgeDate\":null},\"nc\":{\"name\":\"SOS\"},\"np\":{\"name\":\"Telegram\"},\"button\":52965},{\"e\":{\"id\":52964,\"subject\":\"SOS call\",\"submitDate\":1515437749,\"sentDate\":1515437759,\"acknowledgeDate\":null},\"nc\":{\"name\":\"SOS\"},\"np\":{\"name\":\"Telegram\"},\"button\":52964},{\"e\":{\"id\":52963,\"subject\":\"SOS call\",\"submitDate\":1515437631,\"sentDate\":1515437638,\"acknowledgeDate\":null},\"nc\":{\"name\":\"SOS\"},\"np\":{\"name\":\"Telegram\"},\"button\":52963},{\"e\":{\"id\":52962,\"subject\":\"SOS call\",\"submitDate\":1515437321,\"sentDate\":1515437328,\"acknowledgeDate\":null},\"nc\":{\"name\":\"SOS\"},\"np\":{\"name\":\"Telegram\"},\"button\":52962},{\"e\":{\"id\":52961,\"subject\":\"SOS call\",\"submitDate\":1515436395,\"sentDate\":1515436398,\"acknowledgeDate\":null},\"nc\":{\"name\":\"SOS\"},\"np\":{\"name\":\"Telegram\"},\"button\":52961},{\"e\":{\"id\":52960,\"subject\":\"SOS call\",\"submitDate\":1515436120,\"sentDate\":1515436129,\"acknowledgeDate\":null},\"nc\":{\"name\":\"SOS\"},\"np\":{\"name\":\"Telegram\"},\"button\":52960}]}";
        $array = json_decode($a);
        
        $erer = current((Array)current((Array)$array)[0])->id;
        $erered = current((Array)$array)[0]->button;
        
        //$lastId = $array['data'][0];
        //$gdf = max(array_column($array['data'][0], 'id'));


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
        
        $lastId = max(array_column($dataDataTable, 'id'));
        
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
            'dataDataTable' => var_dump($dataDataTable),
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
