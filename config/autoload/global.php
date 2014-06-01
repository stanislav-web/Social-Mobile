<?php
/**
 * Глобальные настройки приложения.
 */

return [
    
    // Параметры старта сессии
    'session' => [
        'remember_me_seconds' => 2419200,
        'use_cookies'       => true,
        'cookie_httponly'   => true,
        'cookie_lifetime'   => 2419200,
        'gc_maxlifetime'    => 2419200,
    ],

    // Параметры локализации
    'languages'=> [
        'ru' => [
            'name' => 'russian',
            'locale' => 'ru_RU',
        ],
        'ua' => [
            'name' => 'ukrainian',
            'locale' => 'ua_UA',
        ],
        'en' => [
            'name' => 'english',
            'locale' => 'en_US',
        ],
    ],
];
