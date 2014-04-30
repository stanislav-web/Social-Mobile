<?php
/**
 * Конфигуратор маршрутизатора текущего модуля (Social)
 * Тут задаются настройки алиасов, а также шаблон обработки URL
 * Записываются все контроллеры в процессе создания приложения
 * Устанавливается путь к приложению по умолчанию
 */
return array(
     /**
      * Пространство имен для всех контроллеров Social
      */
    'controllers' => array(
        'invokables' => array(
            'index.Controller'      => 'Social\Controller\IndexController',     // контроллер главной
            'online.Controller'     => 'Social\Controller\OnlineController',    // контроллер просмотра страниц "онлайн"
            'sign.Controller'       => 'Social\Controller\SignController',      // авторизация, регистрация, восстановление пароля
            'user.Controller'       => 'Social\Controller\UserController',      // контроллер над пользователями и все что с ними связано
            'location.Controller'   => 'Social\Controller\locationController',  // контроллер обслуживания локаций
        ),
    ),
    
    /**
     * Настройки маршрутизатора http
     */
    'router' => array(
        'routes' => array(
            
            'social' => array( // Главная страница
                'type'          => 'Segment',
                'options'       => array(
                'route'         => '[/:lang][/:action]',
                'constraints'   => array(
                    'controller'    => '[a-zA-Z]*',
                    'action'        => '[a-zA-Z]*',
                    'lang'          => '(en|ru|ua)',
                ),
                'defaults' => array(
                    'controller'    => 'index.Controller',
                    'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
            ),

            'sign' => array( // Страница регистрации, авторизации и восстановления аккаунта
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '[/:lang]/sign[/:action]',
                    'constraints'   => array(
                        'controller'    => '[a-zA-Z]*',
                        'action'        => '[a-zA-Z0-3]*',
                        'lang'          => '(en|ru|ua)',
                    ),
                    'defaults' => array(
                        'controller'    => 'sign.Controller',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
            ),
            
           'journal' => array( // Журнал событий пользователя
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '[/:lang]/journal[/]',
                    'constraints'   => array(
                        'lang'          =>  '(en|ru|ua)',
                    ),
                    'defaults' => array(
                        'controller'    => 'user.Controller',
                        'action'        => 'journal',
                    ),
                ),
                'may_terminate' => true,
            ),           
            
            'profile' => array( // Страница управления пользователем (профиль)
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '[/:lang]/profile[/]',
                    'constraints'   => array(
                        'lang'          =>  '(en|ru|ua)',
                    ),
                    'defaults' => array(
                        'controller'    => 'user.Controller',
                        'action'        => 'profile',
                    ),
                ),
                'may_terminate' => true,
            ),

            'users' => array( // Все пользователи
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '[/:lang]/users[/]',
                    'constraints'   => array(
                        'lang'          =>  '(en|ru|ua)',
                    ),
                    'defaults' => array(
                        'controller'    => 'user.Controller',
                        'action'        => 'users',
                    ),
                ),
                'may_terminate' => true,
            ),            
            
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
	    
            'users' => array( // Все пользователи
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '[/:lang]/users[/]',
                    'constraints'   => array(
                        'lang'          =>  '(en|ru|ua)',
                    ),
                    'defaults' => array(
                        'controller'    => 'user.Controller',
                        'action'        => 'users',
                    ),
                ),
                'may_terminate' => true,
            ),            
            
            'user' => array( // Страница пользователя
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '[/:lang]/user',
                    'constraints'   => array(
                        'lang'          =>  '(en|ru|ua)',
                    ),
                    'defaults' => array(
                        'controller'    => 'user.Controller',
                        'action'        => 'index',
                        'lang'          => 'en',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'slug-user' => array(
                        'type'      => 'Segment',
                        'options'   => array(
                            'route' => '[/:slug][/]',
                                'constraints' => array(
                                    'slug' => '[a-zA-Z0-9_-]*',

                                ),
                                'defaults' => array(
                                    'controller'    => 'user.Controller',
                                    'action'        => 'index',
                                )
                        ),
                    ),
                ),                
            ),            
            
            'online' => array( // Страница просмотра пользователей онлайн
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '[/:lang]/online[/:action]',
                    'constraints'   => array(
                        'controller'    => '[a-zA-Z]*',
                        'action'        => '[a-zA-Z0-3]*',
                        'lang'          => '(en|ru|ua)',
                    ),
                    'defaults' => array(
                        'controller'    => 'online.Controller',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                // настраиваю дочерние маршруты
                'child_routes' => array (
                    'male' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/male',
                            'defaults' => array(
                                'action' => 'male',
                            )
                        ),
                    ),
                    'female' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/female',
                            'defaults' => array(
                                'action' => 'female',
                            )
                        ),
                    ),
               ),
            ),
        ),
    ),

    
    /*
     * Пути к языковым файлам
     */
    'translator' => array(
        'locale' => 'ru_RU', // по умолчанию
        'translation_file_patterns' => array(
            array(
                'type'     => 'PhpArray',
                'base_dir' => __DIR__ . '/../language/social',
                'pattern'  => '%s.inc',
                'text_domain' => 'default'
            ),
            array(
                'type'     => 'PhpArray',
                'base_dir' => __DIR__ . '/../language/errors',
                'pattern'  => '%s.inc',
                'text_domain' => 'errors'
            ),
            array(
                'type'     => 'PhpArray',
                'base_dir' => __DIR__ . '/../language/success',
                'pattern'  => '%s.inc',
                'text_domain' => 'success'
            ),
            array(
                'type'     => 'PhpArray',
                'base_dir' => __DIR__ . '/../language/mails',
                'pattern'  => '%s.inc',
                'text_domain' => 'mails'
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
