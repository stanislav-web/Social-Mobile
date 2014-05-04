<?php
/**
 * Директория настроек «config» содержит необходимые настройки,
 * используемые  в ZendModuleManager для загрузки модулей
 * и объединения конфигураций (настройки подключения к БД, меню, ACL и др.)
 */
return array(
    // Модули (сайты) которые используются . Объявляются для пространства имен
    'modules' => array(
        'Cronjob',              // Консоль для запуска планировщика Cron
        'ZendDeveloperTools',   // Панель разработчика
        'SQLProfiler',          // Профайлер SQL запросов
        'ZFTool',               // Модуль диагностики
        'Decoda',               // Сервис BB кодов и эмотиконов
        'Social',               // Социальная сеть с управлением юзерами
        'SwebSocialAuth',       // Мой модуль авторизации в соц сетях
        'Admin',                // Админ панель
        'Plugins',              // Плагины
        'WebinoImageThumb',     // GD2 Thumbnailer
        'ZF2NetteDebug',        // Интегрируемый сервис отладки исключений и ошибок
    ),

    // параметры используемые для слушателя ModuleManager и текущего модуля (сайта)
    'module_listener_options' => array(
        'module_paths' => array(
            // Конфигурация провайдера ZF2 (не изменять)
            './module',
            './vendor',
        ),

        // Файлы конфигурации модулей (по умочанию будут найдены и загружены)
        'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local,service,helper}.php',
        ),

        // кэширование всех настроек
        // 'config_cache_enabled' => true,

        // ключ - как строка для кэша файлов конфигурации
        // 'config_cache_key' => 'ABC0123456789',

        // кэшировние карты классов (у меня это autoload_classmap.php
        //'module_map_cache_enabled' => true,

        // ключ - как строка для кэша карт классов
        //'module_map_cache_key' => '0123456789ABC',

        // путь к закэшированной базе объектов (где все это будет храниться)
        //'cache_dir' => getcwd() . '/data/cache/modules',

        // проверять ли зависимомть модулей?
        // 'check_dependencies' => true,
    ),

    // Использовать собственно созданный Сервис Менеджер? (нет конечно!
    //'service_listener_options' => array(
    //     array(
    //         'service_manager' => $stringServiceManagerName,
    //         'config_key'      => $stringConfigKey,
    //         'interface'       => $stringOptionalInterface,
    //         'method'          => $stringRequiredMethodName,
    //     ),
    // )

   // Инициализация Сервис Менеджера может быть и сдесь
   'service_manager' => array(
        'invokables' => array(
            'Zend\Session\SessionManager' => 'Zend\Session\SessionManager',
        ),       
        'use_defaults' => true,
         'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
        ),
    ),
);