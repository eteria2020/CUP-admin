<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'emailSettings' => [
        'from' => 'servizioclienti@sharengo.eu',
        'replyTo' => 'servizioclienti@sharengo.ue',
        'X-Mailer' => 'Sharengo agent',
        'sharengoNotices' => 'webmaster@philcartechnology.eu',
        'registrationBcc' => 'webmaster@philcartechnology.eu'
    ],
    'csv' => [
        'newPath' => 'data/Csv',
        'addedPath' => 'data/Csv/Added',
        'analyzedPath' => 'data/Csv/Analyzed',
        'tempPath' => 'data/Csv/Temp'
    ],
    'languageSession' => [
        'session' => 'user',
        'offset' => 'lang'
    ],
    'zf2resque' => [
        'redisBackend' => '127.0.0.1:6379',
        'redisDatabase' => 5
    ],
);
