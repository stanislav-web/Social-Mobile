<?php
/**
 * Глобальные настройки приложения.
 */

return array(

    // Параметры старта сессии
    'session' => array(
        'remember_me_seconds' => 2419200,
        'use_cookies'       => true,
        'cookie_httponly'   => true,
        'cookie_lifetime'   => 2419200,
        'gc_maxlifetime'    => 2419200,
    ),

    // Параметры локализации
    'languages'=> array(
        'ru' => array(
            'name' => 'russian',
            'locale' => 'ru_RU',
        ),
        'ua' => array(
            'name' => 'ukrainian',
            'locale' => 'ua_UA',
        ),
        'en' => array(
            'name' => 'english',
            'locale' => 'en_US',
        ),
    ),
);
