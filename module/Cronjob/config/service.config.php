<?php
/**
 * Конфигуратор сервисов модуля, вызываемы с помощью ServiceManager
 * Helper
 * Model
 * Service
 * Validator
 */

return array(

    'factories'     =>  array(

        /* Модели */

        'cronjobLog.Model' => function($serviceManager) { // Модель фиксации работы планировшика Cron
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table = new \Cronjob\Model\CronjobLogModel($dbAdapter);
            return $table;
        },
    ),
);
