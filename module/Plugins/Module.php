<?php

namespace Plugins; // объявяю пространство имен для текущего модуля "Admin"

/**
 * Для наглядности, модуль обеспечивающий базовую функциональность для модуля "Plugins".
 * Так же этот класс является названием корневой папки модуля и пространства имен одновременно
 * Zend Framework 2 имеет менеджер модулей ModuleManager, который служит для загрузки модулей.
 * Он будет искать этот файл в корневой директории модуля (module/Plugins), который должен содержать класс Social\Module.
 * Т.е классы модуля должны принадлежать пространству имен с названием идентичным названию модуля, которым является название директории модуля.
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Plugins/Module.php
 */
class Module
{
    /**
     * getConfig() метод загрузки конфигуратора приложения
     * @access public
     * @return file
     */
    public function getConfig()
    {
        return include __DIR__.'/config/module.config.php';
    }

    /**
     * getServiceConfig() метод загрузки сервисов
     * @access public
     * @return file
     */
    public function getServiceConfig()
    {
        return include __DIR__.'/config/service.config.php';
    }

    /**
     * getViewHelperConfig() метод инициализации помошников вида
     * @access public
     * @return file
     */
    public function getViewHelperConfig()
    {
        return array(
            'invokables' =>  array(
                'socialPlugin'  =>  '\Plugins\Constructor\PluginsConstructor', // виджеты
            ),
        );
    }

    /**
     * getAutoloaderConfig() метод установки автозагрузчиков
     * В моем случае, я подключаю карту классов
     * и устанавливаю пространство имен для MVC директории приложения
     * @access public
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(

            // устанавливаю пространство имен для MVC директории с приложением
            'Zend\Loader\StandardAutoloader'    =>  array(
                'namespaces'=>array(
                    __NAMESPACE__=>__DIR__.'/src/'.__NAMESPACE__,
                ),
            ),
        );
    }
}
