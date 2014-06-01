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
                
        'language.Mpdel' => function($serviceManager) { // сервис выбора стран
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table     = new \DBTranslations\Model\LanguageModel($dbAdapter);
            return $table;
        },
    ),
       
);
