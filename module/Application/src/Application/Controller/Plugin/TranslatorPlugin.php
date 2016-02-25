<?php

namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\I18n\Translator;

class TranslatorPlugin extends AbstractPlugin
{
    /**
     * @var Translator
     */
    protected $translator;


    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function translate($text)
    {
        return $this->translator->translate($text);
    }

    public function getTranslator()
    {
        return $this->translator;
    }
}