<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ErrorController extends AbstractActionController
{

	public function indexAction() {

    }
    
    public function unauthorizedAction() {

        $this->getResponse()->setStatusCode(403);
        
        $I_view = new ViewModel();
        
        $I_view->setTemplate('error/unauthorized');
        
        return $I_view;
        
    }

}
