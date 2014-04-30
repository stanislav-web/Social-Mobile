<?php
/**
 * Конфигуратор модуля интегрируемых виджетов
 */
return array(

    /*
     * Пути к языковым файлам
     */
    'translator' => array(
        'locale' => 'ru_RU', // по умолчанию
        'translation_file_patterns' => array(
            array(
                'type'     => 'PhpArray',
                'base_dir' => __DIR__ . '/../language/plugins',
                'pattern'  => '%s.inc',
                'text_domain' => 'plugins'
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
