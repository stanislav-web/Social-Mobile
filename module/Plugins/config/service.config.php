<?php
/**
 * Конфигуратор сервисов модуля, вызываемы с помощью ServiceManager
 * Model
 */

return array(

    'factories'     =>  array(

        /* Сервисы */

        'plugins.Service' => function($serviceManager) { // сервис выборки плагинов
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            return new \Plugins\Service\PluginsService($dbAdapter);
        },

        /* Модели */

        'bookmarks.Model' => function($serviceManager) { // Модель соц. закладок
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table = new \Plugins\Model\BookmarksModel($dbAdapter);
            return $table;
        },

        'notices.Model' => function($serviceManager) { // Модель для выдачи Правил использования
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table = new \Plugins\Model\NoticesModel($dbAdapter);
            return $table;
        },
                
        'menu.Model' => function($serviceManager) { // Модель для показа менюшек
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table = new \Plugins\Model\MenuModel($dbAdapter);
            return $table;
        },

        'mailtemplates.Model' => function($serviceManager) { // Модель конфигурации почтовых шаблонов
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table = new \Plugins\Model\MailtemplatesConfigModel($dbAdapter);
            return $table;
        },
                
        'events.Model' => function($serviceManager) { // Модель конфигурации почтовых шаблонов
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table = new \Plugins\Model\EventsConfigModel($dbAdapter);
            return $table;
        },
                
        'statistics.Model' => function($serviceManager) { // Модель сбора статистики посещаемости
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table = new \Plugins\Model\StatisticsModel($dbAdapter);
            return $table;
        },
                
        'flashwall.Model' => function($serviceManager) { // Модель флэш стены
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table = new \Plugins\Model\FlashWallModel($dbAdapter);
            return $table;
        },
                
        'usersonline.Model' => function($serviceManager) { // Модель подсчета онлайн пользователей
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table = new \Plugins\Model\UsersOnlineModel($dbAdapter);
            return $table;
        },
    ),

    // Для вызова плагинов через модели, контроллеры итп
    'invokables'    =>  array(
        'plugContent.Service'   =>  '\Plugins\Constructor\PluginsConstructor',  // сервис плагинов, для вызова из контроллеров
        'qrcode.Model'          =>  '\Plugins\Model\QRCodeModel',               // сервис вызова QR кода
        'header.Model'          =>  '\Plugins\Model\HeaderModel',               // сервис вызова HTML заголовка на страницы
        'breadcrumbs.Model'     =>  '\Plugins\Model\BreadcrumbsModel',          // сервис вызова хлебных крошек
    ),
);
