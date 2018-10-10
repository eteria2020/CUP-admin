<?php
namespace Application\Controller;

// Internals
use SharengoCore\Entity\Notifications;
use SharengoCore\Service\NotificationsService;
use SharengoCore\Service\NotificationsProtocolsService;
use SharengoCore\Service\NotificationsCategoriesService;
use SharengoCore\Service\NotificationsCategories\NotificationsCategoriesAbstractFactory;
use SharengoCore\Service\IncidentsService;
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
     * @var IncidentsService
     */
    private $incidentsService;
    

    /**
     * @var Container
     */
    private $datatableFiltersSessionContainer;
    
    //var session
    private $allarm;

    /**
     * @param NotificationsService $notificationsService
     * @param NotificationsProtocolsService $notificationsProtocols
     * @param NotificationsCategoriesService $notificationsCategories
     * @param NotificationsCategoriesAbstractFactory $notificationsCategoriesAbstractFactory
     * @param Container $datatableFiltersSessionContainer
     * #param IncidentsService $incidentsService
     */
    public function __construct(
        NotificationsService $notificationsService,
        NotificationsProtocolsService $notificationsProtocols,
        NotificationsCategoriesService $notificationsCategories,
        NotificationsCategoriesAbstractFactory $notificationsCategoriesAbstractFactory,
        Container $datatableFiltersSessionContainer,
        IncidentsService $incidentsService
    ) {
        $this->notificationsService = $notificationsService;
        $this->notificationsProtocols = $notificationsProtocols;
        $this->notificationsCategories = $notificationsCategories;
        $this->notificationsCategoriesAbstractFactory = $notificationsCategoriesAbstractFactory;
        $this->datatableFiltersSessionContainer = $datatableFiltersSessionContainer;
        $this->incidentsService = $incidentsService;
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

    public function datatableAction(){
        $filters = $this->params()->fromPost();
        $filters['withLimit'] = true;
        $dataDataTable = $this->notificationsService->getDataDataTable($filters);
        $totalNotifications = $this->notificationsService->getTotalNotifications();
        $recordsFiltered = $this->getRecordsFiltered($filters, $totalNotifications);
        
        $checkAllarm = false;
        foreach ($dataDataTable as $data) {
            if (is_null($data['e']['webuser'])) {
                $date = new \DateTime();
                $date = $date->modify("-30 minutes");
                $date = $date->format('U');
                if($date < $data['e']['submitDate']) {
                    $checkAllarm = true;
                    break;
                }
            } else {
                $checkAllarm = false;
            }
        }

        $allarm = new Container('allarm');
        
        if(!$allarm->offsetExists('onOff')){
            $allarm->offsetSet('onOff', "on");
            $onOff ="on";
        }else{
            $onOff = $allarm->offsetGet('onOff');
        }
        
        if(!$allarm->offsetExists('refresh')){
            $allarm->offsetSet('refresh', "on");
            $refresh = "on";
        }else{
            $refresh = $allarm->offsetGet('refresh');
        }
        
        return new JsonModel([
            'draw' => $this->params()->fromQuery('sEcho', 0),
            'checkAllarm' => $checkAllarm,
            'onOff' => $onOff,
            'refresh' => $refresh,
            'recordsTotal' => $totalNotifications,
            'recordsFiltered' => $recordsFiltered,
            'data' => $dataDataTable
        ]);
    }

    public function takeChargeAction() {
        
        $translator = $this->TranslatorPlugin();
        
        // Get the Notification ID from route
        $id = (int) $this->params()->fromRoute('id', 0);
        
        try {
            // Get the notification Object
            $notification = $this->notificationsService->getNotificationById($id);
            if (is_null($notification->getWebuser())) {
                $this->notificationsService->acknowledge($notification, date_create());
                $this->notificationsService->webuser($notification);
                $this->flashMessenger()->addSuccessMessage($translator->translate('SOS preso in carico.'));
            } else{
                $this->flashMessenger()->addErrorMessage($translator->translate('SOS già preso in carico da un altro operatore.'));
            }
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage($translator->translate('Errore durante la presa in carico dell\'SOS.'));
        }

        return $this->redirect()->toRoute('notifications');
    }
    
    public function onOffAllarmAction() {
        $allarm = new Container('allarm');
        $allarm->offsetSet('onOff', $this->params()->fromPost('onOff'));
        return true;
    }
    
    public function autoRefreshNotificationsAction() {
        $allarm = new Container('allarm');
        $allarm->offsetSet('refresh', $this->params()->fromPost('refresh'));
        return true;
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
    
    public function detailsIncidentAction() {

        // Get the Notification ID from route
        $trip_id = (int) $this->params()->fromRoute('id', 0);

        $incident = $this->incidentsService->getIncidentByTrip($trip_id);
        if(count($incident) != 0){
            return new ViewModel([
                'incident' => $incident[0],
            ]);
        }else{
            return new ViewModel([
                'error' => "Non ci sono dettagli per questo urto",
            ]);
        }
    }
}
