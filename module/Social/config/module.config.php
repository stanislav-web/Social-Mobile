<?php
/**
 * Конфигуратор маршрутизатора текущего модуля (Social)
 * Тут задаются настройки алиасов, а также шаблон обработки URL
 * Записываются все контроллеры в процессе создания приложения
 * Устанавливается путь к приложению по умолчанию
 */
return [
     /**
      * Пространство имен для всех контроллеров Social
      */
    'controllers' => [
        'invokables' => [
            'index.Controller'      => 'Social\Controller\IndexController',     // контроллер главной
            'online.Controller'     => 'Social\Controller\OnlineController',    // контроллер просмотра страниц "онлайн"
            'sign.Controller'       => 'Social\Controller\SignController',      // авторизация, регистрация, восстановление пароля
            'user.Controller'       => 'Social\Controller\UserController',      // контроллер над пользователями и все что с ними связано
        ],
    ],
    
    /**
     * Настройки маршрутизатора http
     */
    'router' => [
        'routes' => [
            
            'social' => [ // Главная страница
                'type'          => 'Segment',
                'options'       => [
                'route'         => '[/:lang][/:action]',
                'constraints'   => [
                    'controller'    => '[a-zA-Z]*',
                    'action'        => '[a-zA-Z]*',
                    'lang'          => '(en|ru|ua)',
                ],
                'defaults' => [
                    'controller'    => 'index.Controller',
                    'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
            ],

            'sign' => [ // Страница регистрации, авторизации и восстановления аккаунта
                'type'    => 'Segment',
                'options' => [
                    'route'    => '[/:lang]/sign[/:action]',
                    'constraints'   => [
                        'controller'    => '[a-zA-Z]*',
                        'action'        => '[a-zA-Z0-3]*',
                        'lang'          => '(en|ru|ua)',
                    ],
                    'defaults' => [
                        'controller'    => 'sign.Controller',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
            ],
            
            'logout' => [ // Выход
                'type'          => 'Segment',
                'options'       => [
                'route'         => '/logout',
                'defaults' => [
		    'controller'    => 'sign.Controller',
		    'action'        => 'logout',
                    ],
                ],
            ],             
            
           'journal' => [ // Журнал событий пользователя
                'type'    => 'Segment',
                'options' => [
                    'route'    => '[/:lang]/journal[/]',
                    'constraints'   => [
                        'lang'          =>  '(en|ru|ua)',
                    ],
                    'defaults' => [
                        'controller'    => 'user.Controller',
                        'action'        => 'journal',
                    ],
                ],
                'may_terminate' => true,
            ],           
            
            'profile' => [ // Страница управления пользователем (профиль)
                'type'    => 'Segment',
                'options' => [
                    'route'    => '[/:lang]/profile[/:action]',
                    'constraints'   => [
                        'lang'          =>  '(en|ru|ua)',
                        'action'        =>  '[a-zA-Z]*',
                    ],
                    'defaults' => [
                        'controller'    => 'user.Controller',
                        'action'        => 'profile',
                    ],
                ],
                'may_terminate' => true,
            ],

            'users' => [ // Все пользователи
                'type'    => 'Segment',
                'options' => [
                    'route'    => '[/:lang]/users[/]',
                    'constraints'   => [
                        'lang'          =>  '(en|ru|ua)',
                    ],
                    'defaults' => [
                        'controller'    => 'user.Controller',
                        'action'        => 'users',
                    ],
                ],
                'may_terminate' => true,
            ],            
          
            'user' => [ // Страница пользователя
                'type'    => 'Segment',
                'options' => [
                    'route'    => '[/:lang]/user',
                    'constraints'   => [
                        'lang'          =>  '(en|ru|ua)',
                    ],
                    'defaults' => [
                        'controller'    => 'user.Controller',
                        'action'        => 'index',
                        'lang'          => 'en',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'slug-user' => [
                        'type'      => 'Segment',
                        'options'   => [
                            'route' => '[/:slug][/]',
                                'constraints' => [
                                    'slug' => '[a-zA-Z0-9_-]*',

                                ],
                                'defaults' => [
                                    'controller'    => 'user.Controller',
                                    'action'        => 'index',
                                ]
                        ],
                    ],
                ],                
            ],            
            
            'online' => [ // Страница просмотра пользователей онлайн
                'type'    => 'Segment',
                'options' => [
                    'route'    => '[/:lang]/online[/:action]',
                    'constraints'   => [
                        'controller'    => '[a-zA-Z]*',
                        'action'        => '[a-zA-Z0-3]*',
                        'lang'          => '(en|ru|ua)',
                    ],
                    'defaults' => [
                        'controller'    => 'online.Controller',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                // настраиваю дочерние маршруты
                'child_routes' =>  [
                    'male' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/male',
                            'defaults' => [
                                'action' => 'male',
                            ]
                        ],
                    ],
                    'female' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/female',
                            'defaults' => [
                                'action' => 'female',
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
                'base_dir' => __DIR__ . '/../language/social',
                'pattern'  => '%s.inc',
                'text_domain' => 'default'
            ],
            [
                'type'     => 'PhpArray',
                'base_dir' => __DIR__ . '/../language/errors',
                'pattern'  => '%s.inc',
                'text_domain' => 'errors'
            ],
            [
                'type'     => 'PhpArray',
                'base_dir' => __DIR__ . '/../language/success',
                'pattern'  => '%s.inc',
                'text_domain' => 'success'
            ],
            [
                'type'     => 'PhpArray',
                'base_dir' => __DIR__ . '/../language/mails',
                'pattern'  => '%s.inc',
                'text_domain' => 'mails'
            ],
            [
                'type'     => 'PhpArray',
                'base_dir' => __DIR__ . '/../language/timezones',
                'pattern'  => '%s.inc',
                'text_domain' => 'timezones'
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
