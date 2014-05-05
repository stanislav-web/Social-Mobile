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

        /* Сервисы */

        'countries.Service' => function($serviceManager) { // сервис выбора стран
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');

            $table     = new \Locations\Service\CountriesService($dbAdapter);
            return $table;
        },
        'regions.Service' => function($serviceManager) { // сервис выбора стран
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            return new \Locations\Service\RegionsService($dbAdapter);
        },
        'cities.Service' => function($serviceManager) { // сервис выбора стран
            $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
            $table     = new \Locations\Service\CitiesService($dbAdapter);
            return $table;
        },
    ),
       
);
