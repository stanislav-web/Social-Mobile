<?php
/**
 * Конфигуратор сервисов модуля, вызываемы с помощью ServiceManager
 * Helper
 * Model
 * Service
 * Validator
 */

return array(

    'aliases' => array(
        'Zend\Authentication\AuthenticationService' => 'auth.Helper',
    ),
    
    /* Одноразовые ключи. Singleton */

    'shared'        =>  array(
        'factory.MessageFactory' => false
    ),
    'factories'     =>  array(

        /* Коннект к бд */

        'Zend\Db\Adapter\Adapter' => function ($serviceManager) {
            $config = $serviceManager->get('Configuration');
            if(!isset($config['db'])) return false;
            $adapter = new SQLProfiler\Db\Adapter\ProfilingAdapter(array(
                'driver'    => 'pdo', // драйвер БД
                'driver_options'    => array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'", // кодировка по умолчанию
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // режим отладки
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,  // разрешить буферизацию и кэш
                    'buffer_results' => true
                 ),
                 'dsn'       => 'mysql:dbname='.$config['db']['database'].';host='.$config['db']['hostname'].';port='.$config['db']['port'],
                 'username'  => $config['db']['username'],
                 'password'  => $config['db']['password'],
             ));
             // Запускаю коннект через профилировщик модуль BjyProfiler
             $adapter->setProfiler(new SQLProfiler\Db\Profiler\Profiler);
             $adapter->injectProfilingStatementPrototype();
             return $adapter;
        },

        /* Фабрики почтовых сервисов */

        'factory.SmtpTransport'         => new Social\Factory\SmtpTransportFactory(),   // конфигурация Smtp
        'factory.MessageFactory'        => new Social\Factory\MessageFactory(),         // базовая конфигурация Mail Sender
        'Navigation'                    => new Zend\Navigation\Service\DefaultNavigationFactory(),

        /* Модели */
                
        'user.Model' => function($serviceManager) { // Модель пользователя
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table = new \Social\Model\UserModel($dbAdapter);
            return $table;
        },
                
        'userProfile.Model' => function($serviceManager) { // Модель пользователя
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table = new \Social\Model\UserProfileModel($dbAdapter);
            return $table;
        },
                
        'sign.Model' => function($serviceManager) { // Модель для сервисов авторизации итп
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table = new \Social\Model\signModel($dbAdapter);
            return $table;
         },
                 
        'userEvents.Model' => function($serviceManager) { // Модель для событий
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table = new \Social\Model\EventsUserModel($dbAdapter);
            return $table;
         },
                 
        'online.Model' => function($serviceManager) { // Модель для событий
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table = new \Social\Model\OnlineModel($dbAdapter);
            return $table;
         },
            
        'roles.Model' => function($serviceManager) { // Модель ролей пользователя
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table = new \Social\Model\RolesModel($dbAdapter);
            return $table;
         },                 
                 
        /* Сервисы */

        'authentification.Service' => function($serviceManager) { // Сервис авторизации
            $dbAdapter              = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $dbTableAuthAdapter     = new \Zend\Authentication\Adapter\DbTable(
                                        $dbAdapter,
                                        'zf_users',
                                        'id',
                                        'password',
                                        'MD5(?) AND state = \'1\'' // хэширую пароль и csrf код (УЖЕ ДОЛЖЕН ПЕРЕДАВАТЬСЯ СЮДА)
            );
            //print_r($dbTableAuthAdapter->getResultRowObject());
            $authService = new \Zend\Authentication\AuthenticationService();
            $authService->setAdapter($dbTableAuthAdapter);
            $authService->setStorage($serviceManager->get('auth.Service'));
            return $authService;
        },

        'menuItems.Service' => function($serviceManager) { // Модель для сервисов авторизации итп
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table = new \Social\Service\MenuItemsService($dbAdapter);
            return $table;
         },               
                 
        /* Валидаторы */

        'restore.Validator' => function($serviceManager) { // ваидация формы восстановления аккаунта
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $translator = $serviceManager->get('MvcTranslator');
            return new \Social\Validator\RestoreValidator($dbAdapter, $translator);
        },

        'register.Validator' => function($serviceManager) { // валидация регистрации
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $translator = $serviceManager->get('MvcTranslator');
            return new \Social\Validator\RegisterValidator($dbAdapter, $translator);
        },

        'auth.Validator' => function($serviceManager) { // валидация авторизации
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $translator = $serviceManager->get('MvcTranslator');
            return new \Social\Validator\AuthValidator($dbAdapter, $translator);
        },

        'registerStep1.Validator' => function($serviceManager) { // валидация формы регистрации (шаг 1)
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $translator = $serviceManager->get('MvcTranslator');
            return new \Social\Validator\RegisterStep1Validator($dbAdapter, $translator);
        },

        'registerStep2.Validator' => function($serviceManager) { // валидация формы регистрации (шаг 2)
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $translator = $serviceManager->get('MvcTranslator');
            return new \Social\Validator\registerStep2Validator($dbAdapter, $translator);
        },

        'registerStep3.Validator' => function($serviceManager) { // валидация формы регистрации (шаг 3)
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $translator = $serviceManager->get('MvcTranslator');
            return new \Social\Validator\registerStep3Validator($dbAdapter, $translator);
        },

        'setpassemail.Validator' => function($serviceManager) { // валидация формы восстановления пароля по Email
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $translator = $serviceManager->get('MvcTranslator');
            return new \Social\Validator\setPasswordFormEmailValidator($dbAdapter, $translator);
        },

    ),
    'invokables'    =>  array(
        'auth.Service'      => '\Social\Service\AuthService',       // сервис авторизации
        'mail.Service'      => '\Social\Service\MailService',       // сервис отправки почты
        'ImageMagic.Service' => '\Social\Service\ImageMagicService',  // сервис операций над изображениями Imagemagic
        'GD2.Service'       => '\Social\Service\GD2Service',  // сервис операций над изображениями GD2
        'auth.Helper'       => 'Zend\Authentication\AuthenticationService',        
    ),
);
