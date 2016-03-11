<?php
namespace Application\View\Helper;

use Application\Listener\ChangeLanguageDetector;
use MvLabsMultilanguage\Service\LanguageService;
use Zend\View\Helper\AbstractHelper;

class LanguageMenuHelper extends AbstractHelper
{
    private $languages;
    private $languageService;

    public function __construct(array $languages, LanguageService $languageService)
    {
        $this->languages = $languages;
        $this->languageService = $languageService;
    }

    /**
     * @return LanguageMenu
     */
    public function __invoke()
    {
        $menu = new LanguageMenu();

        $languages = $this->languages;
        $currentLocale = $this->languageService->getTranslator()->getLocale();

        foreach ($languages as $language) {
            $locale = $language['locale'];
            $label = $language['label'];
            $url = "?" . ChangeLanguageDetector::URL_PARAM . "=" . $locale;

            if ($locale == $currentLocale) {
                $menu->setCurrentLanguageLabel($label);
            } else {
                $menu->addLanguage([
                    'code' => $locale,
                    'label' => $label,
                    'url' => $url
                ]);
            }
        }

        return $menu;
    }
}
