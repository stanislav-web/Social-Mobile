<?php
/**
 * Глобальные настройки.
 * Отличие от local.php в том что эти настройки можно сохранить под контроль версий
 * и затереть при обновлении с Git репозитория.
 */

return array(
    // параметры базы данных
    'db' => array(
        'driver'    =>  'pdo',
        'database'  =>  'zf.local',
        'username'  =>  'zend',
        'password'  =>  'zend',
        'hostname'  =>  'localhost',
        'port'      =>  3306 // 4040 (throw a proxy Neor Profiler SQL) 3036 for standart MySQL Server
    ),

    // параметры почты
    'mailer' => array(
        'default_message' => array(
            'from' => array(
                'email' => 'notify@zf.local',
                'name'  => 'Stnislav WEB'
            ),
            'administrator' => array(
                'email' => 'admin@zf.local',
            ),
            'encoding' => 'UTF-8'
        ),
        'smtp_options'   =>  array(
            'host'              =>  'smtp.gmail.com',
            'port'              =>  465,
            'connection_class'  =>  'plain',
            'connection_config' =>  array(
                'username'      =>  'stanisov',
                'password'      =>  'N1LT2Bbj1N9gtmqH',
                'ssl'           =>  'ssl',
            ),
        )
    ),

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
