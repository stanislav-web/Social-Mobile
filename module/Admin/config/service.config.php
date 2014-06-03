<?php
/**
 * Конфигуратор сервисов модуля, вызываемы с помощью ServiceManager
 * Helper
 * Model
 * Service
 * Validator
 */

return [

    /* Сервисы */

    'factories'     =>  [

        /* Модели */

        'MenuItemsModel' => function($serviceManager) { // Модель для формирования меню
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table = new \Admin\Model\MenuItemsModel($dbAdapter);
            return $table;
         },
                 
        'plugintypes.Model' => function($serviceManager) { // Модель с фильтрами плагинов
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table = new \Admin\Model\PlugintypesModel($dbAdapter);
            return $table;
         },                 
    ]
];
