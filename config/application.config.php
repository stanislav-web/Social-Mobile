<?php
/**
 * Директория настроек «config» содержит необходимые настройки,
 * используемые  в ZendModuleManager для загрузки модулей
 * и объединения конфигураций (настройки подключения к БД, меню, ACL и др.)
 */
return [
    // Модули, которые используются . Объявляются для пространства имен
    'modules' => [
	'AssetsBundle',
        'Cronjob',              // Консоль для запуска планировщика Cron
        'SQLProfiler',          // Профайлер SQL запросов
        'ZendDeveloperTools',   // Панель разработчика
        'ZFTool',               // Модуль диагностики
        'Decoda',               // Сервис BB кодов и эмотиконов
        'Social',               // Социальная сеть с управлением юзерами
        'Submissions',          // Сервис платежных систем
        'Locations',            // Модуль управления локациями (города, регионы, страны)
        //'Google',               // Google Services
        //'SwebSocialAuth',       // Мой модуль авторизации в соц сетях
        'Admin',                // Админ панель
        'Plugins',              // Плагины
        'WebSockets',           // WebSockets сервер
        'WebinoImageThumb',     // GD2 Thumbnailer
        'ZF2NetteDebug',        // Интегрируемый сервис отладки исключений и ошибок
    ],

    // параметры используемые для слушателя ModuleManager и текущего модуля (сайта)
    'module_listener_options' => [
        'module_paths' => [
            // Конфигурация провайдера ZF2 (не изменять)
            './module',
            './vendor',
        ],

        // Файлы конфигурации модулей (по умочанию будут найдены и загружены)
        'config_glob_paths' => [
            'config/autoload/{,*.}{global,local,service,helper}.php',
            './vendor/Submissions/config/providers/*.php',
            './module/Admin/config/navigation.config.php',
        ],

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
    ],

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
    
   'service_manager' => [
        'invokables' => [
            'Zend\Session\SessionManager' => 'Zend\Session\SessionManager',
        ],       
        'use_defaults' => true,
         'factories' => [
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
        ],
    ],
];