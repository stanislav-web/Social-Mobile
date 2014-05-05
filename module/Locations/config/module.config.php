<?php
/**
 * Конфигуратор маршрутизатора текущего модуля (Social)
 * Тут задаются настройки алиасов, а также шаблон обработки URL
 * Записываются все контроллеры в процессе создания приложения
 * Устанавливается путь к приложению по умолчанию
 */
return array(
     /**
      * Пространство имен для всех контроллеров модуля Locations
      */
    'controllers' => array(
        'invokables' => array(
            'location.Controller'   => 'Social\Controller\LocationController',  // контроллер обслуживания локаций
        ),
    ),
    
    /**
     * Настройки маршрутизатора http
     */
    'router' => array(
        'routes' => array(
           
            'cities' => array( // Города
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '[/:lang]/cities',
                    'constraints'   => array(
                        'lang'          =>  '(en|ru|ua)',
                    ),
                    'defaults' => array(
                        'controller'    => 'location.Controller',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'short-city' => array(
                        'type'      => 'Segment',
                        'options'   => array(
                            'route' => '/short[/:country][/:region][/:city][/]',
                                'constraints' => array(
                                    'country'	=> '[a-zA-Z]*',
                                    'region'	=> '[a-zA-Z]*',
                                    'city'	=> '[a-zA-Z0-9_-]{1,3}',
                                ),
                                'defaults' => array(
                                    'controller'    => 'location.Controller',
                                    'action'        => 'short',
                                )
                        ),
                    ),
                    'long-city' => array(
                        'type'      => 'Segment',
                        'options'   => array(
                            'route' => '/long[/:long][/]',
                                'constraints' => array(
                                    'long' => '[a-zA-Z0-9_-]*',
                                ),
                                'defaults' => array(
                                    'controller'    => 'location.Controller',
                                    'action'        => 'long',
                                )
                        ),
                    ),
                ),                
            ),           
        ),
    ),

    /*
     * Параметры шаблонов и их публикации
     */

    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true, // показывать ли исключения в 404
        // XHTML11, XHTML1_STRICT, XHTML1_TRANSITIONAL, XHTML1_FRAMESET, HTML4_STRICT, HTML4_STRICT, HTML4_LOOSE, HTML4_FRAMESET, HTML5, CUSTOM
        'doctype'                  => 'HTML5',
        'forbidden_template'       => 'error/403',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',

        // Шаблоны

        'template_map' => include __DIR__  . '../../autoload_templatemap.php',
    ),
);
