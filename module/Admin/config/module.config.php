<?php
/**
 * Конфигуратор маршрутизатора текущего модуля (Admin)
 * Тут задаются настройки алиасов, а также шаблон обработки URL
 * Записываются все контроллеры в процессе создания приложения
 * Устанавливается путь к приложению по умолчанию
 */
return [
     /*
      * Пространство имен для всех контроллеров Admin
      */
    'controllers' => [
        'invokables' => [
            'admin.Controller'      => 'Admin\Controller\AdminController',      // Контроллер администратора
            'plugins.Controller'    => 'Admin\Controller\PluginsController',    // Контроллер управления плагинами
        ],
    ],

    /*
     * Настройки маршрутизатора
     */

    'router' => [
        'routes' => [
            
            'admin' => [ // Главная страница Админки
                'type'          => 'Segment',
                'options'       => [
                'route'         => '/admin[/:lang]',
                'constraints'   => [
                    'lang'          => '(en|ru|ua)',
                ],
                'defaults' => [
		    'controller'    => 'admin.Controller',
		    'action'    => 'index',
                    ],
                ],
            ],
            
            'plugins' => [ // Управление плагинами
                'type'          => 'Segment',
                'options'       => [
                    'route'         => '/admin[/:lang]/plugins[/page/:page]',
                'constraints'   => [
                    'lang'          => '(en|ru|ua)',
                    'page'          => '[0-9]+',
                ],
                'defaults' => [
		    'controller'    => 'plugins.Controller',
		    'action'    => 'index',
		    'lang'      => 'ru',
                    ],
                ],
                'may_terminate' => true, 
                'child_routes' => [
                    
                    'id' => [
                        'type'      => 'Segment',
                        'options'   => [
                            'route' => '/view[/:id]',
                                'constraints' => [
                                    'id' => '[0-9]+',
                                ],
                                'defaults' => [
                                    'controller'    => 'plugins.Controller',
                                    'action'        => 'view',
                                ]
                        ],
                    ],
                    
                ],  
            ],      
	    
            'admin-post' => [ // Post обработчик форм (всегда редирект на контроллер)
                'type'          => 'Segment',
                'options'       => [
                'route'         => '/admin/post[/]',
                'constraints'   => [
                    'controller'    => '[a-zA-Z]*',
                ],
                'defaults' => [
                    'controller'    => 'admin.Controller',
                    'action'        => 'post',
                    ],
                ],
                'may_terminate' => true, 		
            ],	    
        ],
    ],
    
    /*
     * Пути к языковым файлам
     */
    'translator' => [
        'locale' => 'ru_RU', // по умолчанию
        'translation_file_patterns' => [
            [
                'type'     => 'PhpArray',
                'base_dir' => __DIR__ . '/../language/admin',
                'pattern'  => '%s.inc',
                'text_domain' => 'admin'
            ],
            [
                'type'     => 'PhpArray',
                'base_dir' => __DIR__ . '/../language/admin-errors',
                'pattern'  => '%s.inc',
                'text_domain' => 'admin-errors'
            ],
            [
                'type'     => 'PhpArray',
                'base_dir' => __DIR__ . '/../language/mails',
                'pattern'  => '%s.inc',
                'text_domain' => 'admin-mails'
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
    ],
];
