<?php
/**
 * Конфигуратор модуля интегрируемых виджетов
 */
return [

    /*
     * Пути к языковым файлам
     */
    'translator' => [
        'locale' => 'ru_RU', // по умолчанию
        'translation_file_patterns' => [
            [
                'type'     => 'PhpArray',
                'base_dir' => __DIR__ . '/../language/plugins',
                'pattern'  => '%s.inc',
                'text_domain' => 'plugins'
            ],
        ],
    ],

    /*
     * Параметры шаблонов и их публикации
     */
    'view_manager' => [
        
        // Шаблоны

        'template_map' => include __DIR__  . '../../autoload_templatemap.php',
    ],
];
