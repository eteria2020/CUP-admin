<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use BjyAuthorize\View\RedirectionStrategy;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Zend\Validator\AbstractValidator;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $application = $e->getApplication();
        $eventManager = $application->getEventManager();
        $serviceManager = $application->getServiceManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $options = $serviceManager->get('zfcuser_module_options');

        // Add the default entity driver only if specified in configuration
        if ($options->getEnableDefaultEntities()) {
            $chain = $serviceManager->get('doctrine.driver.orm_default');
            $chain->addDriver(new XmlDriver(__DIR__ . '/config/xml/zfcuserdoctrineorm'), 'ZfcUserDoctrineORM\Entity');
        }

        // BjyAuthorize redirection strategy
        $strategy = new RedirectionStrategy();

        // Change default layout if user is not logged
        $auth = $serviceManager->get('zfcuser_auth_service');
        if (!$auth->hasIdentity()) {
            $eventManager->attach("dispatch", function ($e) {
                $I_controller = $e->getTarget();
                $I_controller->layout('layout/layout_login');
            });

        } else {
            // if user is not logged, set unauthorized route name
            $strategy->setRedirectRoute('unauthorized');
        }

        $eventManager->attach($strategy);

        $translator     = $serviceManager->get('translator');
        $translator->addTranslationFile(
            'phpArray',
            'vendor/zendframework/zendframework/resources/languages/it/Zend_Validate.php',
            'default',
            'it_IT'
        );

        $translator->addTranslationFile(
            'phpArray',
            'vendor/zendframework/zendframework/resources/languages/fr/Zend_Validate.php',
            'default',
            'fr_FR'
        );

        $translator->addTranslationFile(
            'phpArray',
            'vendor/zendframework/zendframework/resources/languages/de/Zend_Validate.php',
            'default',
            'de_DE'
        );

        $translator->addTranslationFile(
            'phpArray',
            'vendor/zendframework/zendframework/resources/languages/es/Zend_Validate.php',
            'default',
            'es_ES'
        );

        $translator->addTranslationFile(
            'phpArray',
            'vendor/zendframework/zendframework/resources/languages/hu/Zend_Validate.php',
            'default',
            'hu_HU'
        );

        $translator->addTranslationFile(
            'phpArray',
            'vendor/zendframework/zendframework/resources/languages/pl/Zend_Validate.php',
            'default',
            'pl_PL'
        );

        $translator->addTranslationFile(
            'phpArray',
            'vendor/zendframework/zendframework/resources/languages/pt_BR/Zend_Validate.php',
            'default',
            'pt_PT'
        );

        $translator->addTranslationFile(
            'phpArray',
            'vendor/zendframework/zendframework/resources/languages/ru/Zend_Validate.php',
            'default',
            'ru_RU'
        );

        $translator->addTranslationFile(
            'phpArray',
            'vendor/zendframework/zendframework/resources/languages/tr/Zend_Validate.php',
            'default',
            'tr_TR'
        );

        $translator->addTranslationFile(
            'phpArray',
            'vendor/zendframework/zendframework/resources/languages/zh/Zend_Validate.php',
            'default',
            'zh_CN'
        );

        AbstractValidator::setDefaultTranslator($translator);

        // Add ACL information to Navigation view helper
        $authorize = $serviceManager->get('BjyAuthorize\Service\Authorize');
        try {
            \Zend\View\Helper\Navigation::setDefaultAcl($authorize->getAcl());
            \Zend\View\Helper\Navigation::setDefaultRole($authorize->getIdentity());
        } catch (\Doctrine\DBAL\DBALException $exception) {
            // database tables not yet initialized
        }

        $changeLanguageDetector = $serviceManager->get('ChangeLanguageDetector.listener');
        $eventManager->attachAggregate($changeLanguageDetector);

        $foreignDriverLicenseValidListener = $serviceManager->get('SharengoCore\Listener\ForeignDriverLicenseValidListener');
        $eventManager->getSharedManager()->attachAggregate($foreignDriverLicenseValidListener);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    // View Helper Configuration
    public function getViewHelperConfig()
    {
        return [
            'factories' => [
                'languageMenuHelper' => 'Application\\View\\Helper\\LanguageMenuHelperFactory'
            ],
        ];
    }
}
