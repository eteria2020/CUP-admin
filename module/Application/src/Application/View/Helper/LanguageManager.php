<?php
namespace Application\View\Helper;

use Application\Service\UserLanguageService;
use Zend\View\Helper\AbstractHelper;

class LanguageManager extends AbstractHelper
{
    
    private $languages;
    private $userLanguageService;

    public function __construct(array $languages, UserLanguageService $userLanguageService)
    {
        $this->languages = $languages;
        $this->userLanguageService = $userLanguageService;
    }
    
    public function __invoke()
    {
        $menu = new \stdClass();
        $menu->languages = [];
        
        $languages = $this->languages;

        foreach ($languages as $language) {
            $locale = $language['locale'];
            $lang = $language['lang'];
            $url = "?change-language=" . $lang;
            
            $menu->languages[] = [
                'code' => $locale,
                'label' => $language['label'],
                'url' => $url
            ];
        }

        $menu->currentLanguage = $this->userLanguageService->getCurrentLang();

        $menu->currentLanguage = [
            'code' => $this->languages[$this->userLanguageService->getcurrentLang()]['locale'],
            'label' => $this->languages[$this->userLanguageService->getcurrentLang()]['label'],
        ];
        
        return $menu;
    }
}
