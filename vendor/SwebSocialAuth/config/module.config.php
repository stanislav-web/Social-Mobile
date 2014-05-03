<?php
/**
 * Конфигуратор модуля интегрируемых виджетов
 */

return array(
     /**
      * Пространство имен для всех контроллеров модуля SwebSocialAuth
      */
    'controllers' => array(
        'invokables' => array(
            'social.Controller' => 'SwebSocialAuth\Controller\SocialController', // контроллер Auth
        ),
    ),
    
     /**
      * Пространство имен для ViewHelper'ов помошников вида
      */    
    'view_helpers' => array(
        'invokables'=> array(
            'socialAuth' => 'SwebSocialAuth\View\Helper\SocialAuth'  
        )
    ),    
    
    /**
     * Настройки маршрутизатора http
     */
    'router' => array(
        'routes' => array(
            
            'socialauth' => array( // Роут авторизации модуля SwebSocialAuth
                'type'          => 'Segment',
                'options'       => array(
                'route'         => '/authservice[/]',
                'defaults' => array(
		    'controller'    => 'social.Controller',
		    'action'    => 'auth',
                    ),
                ),
            ),
        ),
    ),
    
    /*
     * Параметры шаблонов и их публикации
     */
    'view_manager' => array(
        
        // Шаблоны

        'template_map' => include __DIR__  . '../../autoload_templatemap.php',
    ),    
);
