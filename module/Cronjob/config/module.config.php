<?php
/**
 * Конфигуратор маршрутизатора текущего модуля (Social)
 * Тут задаются настройки алиасов, а также шаблон обработки URL
 * Записываются все контроллеры в процессе создания приложения
 * Устанавливается путь к приложению по умолчанию
 */
return array(
     /**
      * Пространство имен для всех Cron консольных контроллеров
      */
    'controllers' => array(
        'invokables' => array(
            'Cronjob\Controller\CronjobUsers'    => 'Cronjob\Controller\CronjobUsersController',   // вызов контоллера управления пользователями
        ),
    ),

    /**
     * Настройка консольного вывода
     */
    'console' => array(
        'router' => array(
            'routes' => array(
                
                // Обновление пользователей онлайна
                'user-update-online' => array(
                    'options'   => array(
                        'route' => 'console-user updateonline [--verbose|-v] <type>',
                        'defaults' => array(
                            'controller'    => 'Cronjob\Controller\CronjobUsers',
                            'action'        => 'online',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
