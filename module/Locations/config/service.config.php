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
        
        'memcache.Service' => function ($serviceManager) {  // сервис кэширования локаций через Memcache
            $memcache = new \Zend\Cache\Storage\Adapter\Memcache($serviceManager->get('memcache.Options'));
            return $memcache; // Не путать службу с Memcached! Если в PHP стоит Memcached значит тут уместен адаптер Zend\Cache\Storage\Adapter\Memcached
        },
                
        'memcache.Options' => function ($serviceManager) { // Создаю сервис с конфигурацией адаптера Memcache
            $MemcacheResourceManager = new \Zend\Cache\Storage\Adapter\MemcacheResourceManager();
            $MemcacheResourceManager->addServer('1', array('127.0.0.1', 11211));        
            return  new \Zend\Cache\Storage\Adapter\MemcacheOptions(array(
                    'resource_manager' => $MemcacheResourceManager,
                    'resource_id'      => '1',
                    'namespace'        => 'MEMCACHE',
                    'ttl'              => 60*60*24*7,
                )
            );
        },
                
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
