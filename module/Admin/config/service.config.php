<?php
/**
 * Конфигуратор сервисов модуля, вызываемы с помощью ServiceManager
 * Helper
 * Model
 * Service
 * Validator
 */

return array(

    /* Сервисы */

    'factories'     =>  array(

        /* Модели */

        'MenuItemsModel' => function($serviceManager) { // Модель для сервисов авторизации итп
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table = new \Admin\Model\MenuItemsModel($dbAdapter);
            return $table;
         },

        /* Валидаторы */

        'adminAuth.Validator' => function($serviceManager) { // валидация авторизации
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $translator = $serviceManager->get('MvcTranslator');
            return new \Admin\Validator\AuthValidator($dbAdapter, $translator);
        },

    ),
);
