<?php
/**
 * Конфигуратор маршрутизатора текущего модуля (Admin)
 * Тут задаются настройки алиасов, а также шаблон обработки URL
 * Записываются все контроллеры в процессе создания приложения
 * Устанавливается путь к приложению по умолчанию
 */
return array(
     /*
      * Пространство имен для всех контроллеров Admin
      */
    'controllers' => array(
        'invokables' => array(
            'admin.Controller'      => 'Admin\Controller\AdminController',     // администрирование соц сети
        ),
    ),

    /*
     * Настройки маршрутизатора
     */

    'router' => array(
        'routes' => array(

            'admin' => array( // Главная страница Админки
                'type'          => 'Segment',
                'options'       => array(
                'route'         => '/admin[/:lang][/:action][/status/:status][/page/:page][/perPage/:perPage][/sortBy/:sortBy][/sortDir/:sortDir][/filterLetter/:filterLetter]',
                'constraints'   => array(
                    'controller'    => '[a-zA-Z]*',
                    'action'        => '[a-zA-Z]*',
                    'lang'          => '(en|ru|ua)',
                    'action'        => '[a-zA-Z]*',
                    'page'	    => '[0-9]*',
                    'perPage'       => '[0-9]*',
                ),
                'defaults' => array(
		    '__NAMESPACE__' => 'Social\Admin',
		    'controller'    => 'admin.Controller',
		    'action'    => 'index',
		    'status'	=> 'all',
		    'page'	=> 1,
		    'perPage'	=> 10,
		    'sortBy'	=> "id",
		    'sortDir'	=> "asc",
		    'filterLetter'	=> "",
                    ),
                ),
            ),
	    
            'admin-post' => array( // Post обработчик форм (всегда редирект на контроллер)
                'type'          => 'Segment',
                'options'       => array(
                'route'         => '/admin/post[/]',
                'constraints'   => array(
                    'controller'    => '[a-zA-Z]*',
                ),
                'defaults' => array(
                    'controller'    => 'admin.Controller',
                    'action'        => 'post',
                    ),
                ),
                'may_terminate' => true, 		
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
                'base_dir' => __DIR__ . '/../language/admin',
                'pattern'  => '%s.inc',
                'text_domain' => 'admin'
            ),
            array(
                'type'     => 'PhpArray',
                'base_dir' => __DIR__ . '/../language/admin-errors',
                'pattern'  => '%s.inc',
                'text_domain' => 'admin-errors'
            ),
            array(
                'type'     => 'PhpArray',
                'base_dir' => __DIR__ . '/../language/mails',
                'pattern'  => '%s.inc',
                'text_domain' => 'admin-mails'
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
