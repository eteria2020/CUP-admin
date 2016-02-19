<?php
namespace Application\Controller;

use Application\Form\ConfigurationsForm;
use SharengoCore\Entity\Configurations;
use SharengoCore\Exception\ConfigurationSaveAlarmException;
use SharengoCore\Service\ConfigurationsService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Class ConfigurationsController
 * @package Application\Controller
 */
class ConfigurationsController extends AbstractActionController
{
    /**
     * @var ConfigurationsService
     */
    private $configurationsService;

    /**
     * @var ConfigurationsForm
     */
    private $configurationsForm;

    /**
     * ConfigurationsController constructor.
     *
     * @param ConfigurationsService $configurationsService
     * @param ConfigurationsForm    $configurationsForm
     */
    public function __construct(ConfigurationsService $configurationsService, ConfigurationsForm $configurationsForm)
    {
        $this->configurationsService = $configurationsService;
        $this->configurationsForm = $configurationsForm;
    }

    /**
     * @return ViewModel
     */
    public function manageAlarmAction()
    {
        $translator = $this->TranslatorPlugin();
        $form = $this->configurationsForm;
        $alarms = [];
        $alarms['configurations'] = $this->configurationsService->getConfigurationsBySlug(Configurations::ALARM, true);
        $form->setData($alarms);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);

            if ($form->isValid()) {

                try {

                    $this->configurationsService->saveDataManageAlarm($form->getData());
                    $this->flashMessenger()->addSuccessMessage($translator->translate('Configurazione salvata con successo!'));

                } catch (ConfigurationSaveAlarmException $e) {

                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                }

                return $this->redirect()->toRoute('configurations/manage-alarm');
            }
        }

        return new ViewModel([
            'configurations'     => $this->configurationsService->getConfigurationsBySlug(Configurations::ALARM),
            'configurationsForm' => $form
        ]);
    }
}
