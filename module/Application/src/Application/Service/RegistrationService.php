<?php

namespace Application\Service;

use Doctrine\ORM\EntityManager;
use SharengoCore\Service\EmailService;
use Zend\Mvc\I18n\Translator;
use Zend\View\HelperPluginManager;

final class RegistrationService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var array
     */
    private $emailSettings;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var HelperPluginManager
     */
    private $viewHelperManager;

    /**
     * @param EntityManager $entityManager
     * @param array $emailSettings
     * @param EmailService $emailService
     * @param Translator $translator
     * @param HelperPluginManager $viewHelperManager
     */
    public function __construct(
        EntityManager $entityManager,
        array $emailSettings,
        EmailService $emailService,
        Translator $translator,
        HelperPluginManager $viewHelperManager
    ) {
        $this->entityManager = $entityManager;
        $this->emailSettings = $emailSettings;
        $this->emailService = $emailService;
        $this->translator = $translator;
        $this->viewHelperManager = $viewHelperManager;
    }

    /**
     * @param string $email
     * @param string $name
     * @param string $surname
     * @param string $hash
     * @param string $language
     */
    public function sendEmail($email, $name, $surname, $hash, $language)
    {
        /** @var callable $url */
        $url = $this->viewHelperManager->get('url');
        /** @var callable $serverUrl */
        $serverUrl = $this->viewHelperManager->get('serverUrl');

        $writeTo = $this->emailSettings['from'];
        $mail = $this->emailService->getMail(1, $language);
        $content = sprintf(
            $mail->getContent(),
            $name,
            $surname,
            $serverUrl().$url('signup_insert').'?user='.$hash//,
            //$writeTo
        );

        $attachments = [];

        $this->emailService->sendEmail(
            $email,
            $mail->getSubject(), //'Conferma la tua iscrizione a Share’nGo',
            $content,
            $attachments
        );

        $this->emailService->sendEmail(
            $this->emailSettings['sharengoNotices'],
            $mail->getSubject(),//'Conferma la tua iscrizione a Share’nGo',
            $content,
            $attachments
        );
    }


}
