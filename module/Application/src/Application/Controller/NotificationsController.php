<?php
namespace Application\Controller;

// Internals
use SharengoCore\Entity\Notifications;
use SharengoCore\Service\NotificationsService;
use SharengoCore\Service\NotificationsProtocolsService;
use SharengoCore\Service\NotificationsCategoriesService;
// Externals
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Doctrine\DBAL\Exception\DriverException;

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
     * @var Container
     */
    private $datatableFiltersSessionContainer;

    /**
     * @param NotificationsService $notificationsService
     * @param NotificationsProtocolsService $notificationsProtocols
     * @param NotificationsCategoriesService $notificationsCategories
     * @param Container $datatableFiltersSessionContainer
     */
    public function __construct(
        NotificationsService $notificationsService,
        NotificationsProtocolsService $notificationsProtocols,
        NotificationsCategoriesService $notificationsCategories,
        Container $datatableFiltersSessionContainer
    ) {
        $this->notificationsService = $notificationsService;
        $this->notificationsProtocols = $notificationsProtocols;
        $this->notificationsCategories = $notificationsCategories;
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

        if (!$notification instanceof Notifications){
            $this->flashMessenger()->addErrorMessage($translator->translate('La notifica non è stata trovata.'));
        }

        return new ViewModel([
            'notification' => $notification,
        ]);
    }

    public function datatableAction()
    {
        $filters = $this->params()->fromPost();
        $filters['withLimit'] = true;
        $dataDataTable = $this->notificationsService->getDataDataTable($filters);
        $totalNotifications = $this->notificationsService->getTotalNotifications();
        $recordsFiltered = $this->getRecordsFiltered($filters, $totalNotifications);

        return new JsonModel([
            'draw' => $this->params()->fromQuery('sEcho', 0),
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

            // Make the acknowledgment
            $date = $this->notificationsService->acknowledge($notification);

            $this->flashMessenger()->addSuccessMessage($translator->translate('Presa visione della notifica con ID: ') . ' ' . $id . '.' );
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage($translator->translate('Errore durante la presa visione della notifica.'));
        }

        return new JsonModel([
            'dateTimeZ' => $date,
            'dateTimeStamp' => $date->getTimestamp()
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
