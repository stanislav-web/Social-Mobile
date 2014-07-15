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
            'admin.Controller'          => 'Admin\Controller\AdminController',          // Контроллер администратора
            'users.Controller'          => 'Admin\Controller\UsersController',          // Контроллер управления пользователями
            'plugins.Controller'        => 'Admin\Controller\PluginsController',        // Контроллер управления плагинами
            'distributions.Controller'  => 'Admin\Controller\DistributionsController',  // Контроллер управления масштабными рассылками
            'locations.Controller'	=> 'Admin\Controller\LocationsController',	// Контроллер управления локациями
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

            'users' => [ // Управление пользователями
                'type'          => 'Segment',
                'options'       => [
                    'route'         => '/admin[/:lang]/users[/page/:page]',
                'constraints'   => [
                    'lang'          => '(en|ru|ua)',
                    'page'          => '[0-9]+',
                ],
                'defaults' => [
		    'controller'    => 'users.Controller',
		    'action'    => 'index',
		    'lang'      => 'ru',
		    'page'      => '1',
		    'order'     => 'id',
                    ],
                ],
                'may_terminate' => true, 
                'child_routes' => [
                    
                    'edit' => [
                        'type'      => 'Segment',
                        'options'   => [
                            'route' => '/edit[/:id]',
                                'constraints' => [
                                    'id' => '[0-9]+',
                                ],
                                'defaults' => [
                                    'controller'    => 'users.Controller',
                                    'action'        => 'edit',
                                ]
                        ],
                    ],
                    'view' => [
                        'type'      => 'Segment',
                        'options'   => [
                            'route' => '/view[/:id]',
                                'constraints' => [
                                    'id' => '[0-9]+',
                                ],
                                'defaults' => [
                                    'controller'    => 'users.Controller',
                                    'action'        => 'view',
                                ]
                        ],
                    ],                    
                    'json' => [
                        'type'      => 'Segment',
                        'options'   => [
                            'route' => '/json',
                                'constraints' => [
                                ],
                                'defaults' => [
                                    'controller'    => 'users.Controller',
                                    'action'        => 'json',
                                ]
                        ],
                    ],                     
                ],  
            ],            
            
            'plugins' => [ // Управление плагинами
                'type'          => 'Segment',
                'options'       => [
                    'route'         => '/admin[/:lang]/plugins[/page/:page]',
                'constraints'   => [
                    'lang'          =>  '(en|ru|ua)',
                    'page'          =>  '[0-9]+',
                ],
                'defaults' => [
		    'controller'    => 'plugins.Controller',
		    'action'    => 'index',
		    'lang'      => 'ru',
		    'page'      => '1',
                    ],
                ],
                'may_terminate' => true, 
                'child_routes' => [
                    
                    'edit' => [
                        'type'      => 'Segment',
                        'options'   => [
                            'route' => '/edit[/:id]',
                                'constraints' => [
                                    'id' => '[0-9]+',
                                ],
                                'defaults' => [
                                    'controller'    => 'plugins.Controller',
                                    'action'        => 'edit',
                                ]
                        ],
                    ],
                    'add' => [
                        'type'      => 'Segment',
                        'options'   => [
                            'route' => '/add',
                                'constraints' => [
                                ],
                                'defaults' => [
                                    'controller'    => 'plugins.Controller',
                                    'action'        => 'add',
                                ]
                        ],
                    ],                    
                ],  
            ],      
	    
	    'locations' => [ // Управление локациями
                'type'          => 'Segment',
                'options'       => [
                    'route'         => '/admin[/:lang]/locations[/page/:page]',
                'constraints'   => [
                    'lang'          => '(en|ru|ua)',
                    'page'          => '[0-9]+',
                ],
                'defaults' => [
		    'controller'    => 'locations.Controller',
		    'action'    => 'index',
		    'lang'      => 'ru',
		    'page'      => '1',
                    ],
                ],
                'may_terminate' => true, 
                'child_routes' => [
                    
                    'compiler' => [
                        'type'      => 'Segment',
                        'options'   => [
                            'route' => '/compiler',
                                'defaults' => [
                                    'controller'    => 'locations.Controller',
                                    'action'        => 'compiler',
                            ]
                        ],
                    ],
                ],  
            ],
	    
            'distributions' => [ // Управление масштабными рассылками
                'type'          => 'Segment',
                'options'       => [
                    'route'         => '/admin[/:lang]/distributions[/page/:page]',
                'constraints'   => [
                    'lang'          => '(en|ru|ua)',
                    'page'          => '[0-9]+',
                ],
                'defaults' => [
		    'controller'    => 'distributions.Controller',
		    'action'    => 'index',
		    'lang'      => 'ru',
		    'page'      => '1',
                    ],
                ],
                'may_terminate' => true, 
                'child_routes' => [
                    
                    'provider' => [
                        'type'      => 'Segment',
                        'options'   => [
                            'route' => '/view[/:provider]',
                                'constraints' => [
                                    'provider' => '[a-zA-Z0-9_-]*',
                                ],
                                'defaults' => [
                                    'controller'    => 'distributions.Controller',
                                    'action'        => 'view',
                                ]
                        ],
                    ],
                ],  
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
                'base_dir' => __DIR__ . '/../language/admin-messages',
                'pattern'  => '%s.inc',
                'text_domain' => 'admin-messages'
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
        
        // Json предусмотренно в этом модуле
        
        'strategies' => [
            'ViewJsonStrategy',
        ],        
    ],
];
