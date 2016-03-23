<?php

/*
 * copy this file to config/autoload/multilanguage.global.php
 *
 * we provide an example of the configuation needed to use the module.
 * Overwrite according to your needs
 */

return [
    'multilanguage' => [
        // language ranges that are allowed in the application.
        // the keys must correspond to the value used in the pattern key in the
        // transaltor/translation_file_patterns configuration key
        'allowed_languages' => [
            'it_IT',
            'en_US',
            'fr_FR',
            'zh_CN',
            'de_DE',
            'es_ES',
            'hu_HU',
            'pl_PL',
            'pt_PT',
            'ru_RU',
            'tr_TR',
        ],
        // listeners used to determine the appropriate language range for the
        // application.
        // The order is important!
        'listeners' => [
            'LanguageFromSessionDetectorListener',
            'FilterByConfigurationDetectorListener',
            'ReturnFirstValueDetectorListener'
        ]
    ]
];
