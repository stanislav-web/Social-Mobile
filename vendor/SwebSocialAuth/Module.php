<?php

namespace SwebSocialAuth;
use SwebSocialAuth\View\Helper\SocialAuth;

/**
 * Модуль авторизации / регистрации через социальные службы.
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /vendor/SwebSocial/Module.php
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
     * getAutoloaderConfig() метод установки автозагрузчиков
     * В моем случае, я подключаю карту классов
     * и устанавливаю пространство имен для MVC директории приложения
     * @access public
     * @return array
     */    
    public function getAutoloaderConfig()
    {
        return array(
            // авозагрузка классов
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            // устанавливаю пространство имен для MVC директории с приложением
            'Zend\Loader\StandardAutoloader'    =>  array(
                'namespaces'=>array(
                    __NAMESPACE__=>__DIR__.'/src/'.__NAMESPACE__,
                ),
            ),
        );
    }
}