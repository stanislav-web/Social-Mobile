<?php
/**
 * Конфигуратор маршрутизатора текущего модуля (Social)
 * Тут задаются настройки алиасов, а также шаблон обработки URL
 * Записываются все контроллеры в процессе создания приложения
 * Устанавливается путь к приложению по умолчанию
 */
return [
     /**
      * Пространство имен для всех контроллеров модуля Locations
      */
    'controllers' => [
        'invokables' => [
            'locations.Controller'   => 'Locations\Controller\LocationsController',  // контроллер обслуживания локаций
        ],
    ],
    
    /**
     * Настройки маршрутизатора http
     */
    'router' => [
        'routes' => [
           
            'locations' => [ // Управление пользователями
                'type'          => 'Segment',
                'options'       => [
                    'route'         => '/locations[/:lang]',
                'constraints'   => [
                    'lang'          => '(en|ru|ua)',
                ],
                'defaults' => [
		    'controller'    => 'locations.Controller',
		    'action'    => 'index',
		    'lang'      => 'ru',
                    ],
                ],
                'may_terminate' => true, 
                'child_routes' => [
                    'json' => [
                        'type'      => 'Segment',
                        'options'   => [
                            'route' => '/json',
                            'constraints' => [
                            ],
                            'defaults' => [
                                'controller'    => 'locations.Controller',
                                'action'        => 'json',
                            ]
                        ],
                    ],                     
                ],  
            ], 
            
            
            'cities' => [ // Города
                'type'    => 'Segment',
                'options' => [
                    'route'    => '[/:lang]/cities',
                    'constraints'   => [
                        'lang'          =>  '(en|ru|ua)',
                    ],
                    'defaults' => [
                        'controller'    => 'locations.Controller',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'short-city' => [
                        'type'      => 'Segment',
                        'options'   => [
                            'route' => '/short[/:country][/:region][/:city][/]',
                                'constraints' => [
                                    'country'	=> '[a-zA-Z]*',
                                    'region'	=> '[a-zA-Z]*',
                                    'city'	=> '[a-zA-Z0-9_-]{1,3}',
                                ],
                                'defaults' => [
                                    'controller'    => 'locations.Controller',
                                    'action'        => 'short',
                                ]
                        ],
                    ],
                    'long-city' => [
                        'type'      => 'Segment',
                        'options'   => [
                            'route' => '/long[/:long][/]',
                                'constraints' => [
                                    'long' => '[a-zA-Z0-9_-]*',
                                ],
                                'defaults' => [
                                    'controller'    => 'locations.Controller',
                                    'action'        => 'long',
                                ]
                        ],
                    ],
                ],                
            ],           
        ],
    ],

    /*
     * Параметры шаблонов и их публикации
     */

    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true, // показывать ли исключения в 404
        // XHTML11, XHTML1_STRICT, XHTML1_TRANSITIONAL, XHTML1_FRAMESET, HTML4_STRICT, HTML4_STRICT, HTML4_LOOSE, HTML4_FRAMESET, HTML5, CUSTOM
        'doctype'                  => 'HTML5',
        'forbidden_template'       => 'error/403',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',

        // Шаблоны

        'template_map' => include __DIR__  . '../../autoload_templatemap.php',
        
        // Json предусмотренно в этом модуле
        
        'strategies' => [
            'ViewJsonStrategy',
        ],           
    ],
];
