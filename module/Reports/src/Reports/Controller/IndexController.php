<?php
/**
 * Zend Framework (http://framework.zend.com/).
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 *
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Reports\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Service\UserLanguageService;

class IndexController extends AbstractActionController
{
    /**
     * @var Application\Service\UserLanguageService
     */
    private $userLanguageService;

    public function __construct(
        UserLanguageService $userLanguageService
    ) {
        $this->userLanguageService = $userLanguageService;
    }

    public function tripsAction()
    {
        $this->layout('layout/reports');

        $viewModel = new ViewModel([
            'userLanguage' => $this->userLanguageService->getCurrentLang(),
        ]);

        return $viewModel;
    }

    public function mapAction()
    {
        $this->layout('layout/reports');

        $viewModel = new ViewModel([
            'userLanguage' => $this->userLanguageService->getCurrentLang(),
        ]);

        return $viewModel;
    }

    public function routesAction()
    {
        $this->layout('layout/reports');

        $tripid = $this->params()->fromRoute('tripid', null);

        $viewModel = new ViewModel([
            'tripid' => $tripid,
            'userLanguage' => $this->userLanguageService->getCurrentLang(),
        ]);

        return $viewModel;
    }

    public function liveAction()
    {
        $this->layout('layout/reports');

        $viewModel = new ViewModel([
            'userLanguage' => $this->userLanguageService->getCurrentLang(),
        ]);

        return $viewModel;
    }

    public function tripscityAction()
    {
        $this->layout('layout/reports');

        $city = $this->params()->fromRoute('id', 0);

        $viewModel = new ViewModel([
            'city' => $city,
            'userLanguage' => $this->userLanguageService->getCurrentLang(),
        ]);

        return $viewModel;
    }
}
