<?php

/**
 * Точка входа в приложение
 * Инициализация автозагрузчика библиотек
 * Инициализация глобальных настроек ZendFramework 2
 */

// Для ZendDeveloperTools
define('DS', DIRECTORY_SEPARATOR); // разделитель директорий по умолчанию
define('DOCUMENT_ROOT', getcwd()); // корневой путь сайта
define('REQUEST_MICROTIME', microtime(true));
define('HTTP_HOST', 'zf.local');

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

date_default_timezone_set('Europe/Berlin');
// Определяю текущую директорию как корневую
chdir(dirname(__DIR__));

// Настройки автозагрузки
require 'init_autoloader.php';
// Запуск приложения
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
