<?php
/**
 * Webino (http://www.webino.org/)
 *
 * @copyright   Copyright (c) 2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license     New BSD License
 * @package     ZF2NetteDebug
 */

namespace ZF2NetteDebug;

use Zend\Mvc\MvcEvent;
use Nette\Diagnostics\Debugger;

/**
 * @category    Webino
 * @package     Zf2NetteDebug
 * @author      Peter Bačinský <peter@bacinsky.sk>
 */
class Module
{
    /**
     * Setup Nette\Debugger with options
     *
     * @param MvcEvent $e
     * @return void
     */
    public function onBootstrap(MvcEvent $e)
    {
        $app    = $e->getApplication();
        $config = $app->getConfig();

        if (empty($config['nette_debug'])
            || empty($config['nette_debug']['enabled'])
        ) return;

        require __DIR__ . '/src/Nette/Diagnostics/exceptions.php';
        
        array_key_exists('bar', $config['nette_debug']) or
            $config['nette_debug']['bar'] = true;

        array_key_exists('mode', $config['nette_debug']) or
            $config['nette_debug']['mode'] = null;
        
        array_key_exists('log', $config['nette_debug']) or
            $config['nette_debug']['log'] = null;

        array_key_exists('email', $config['nette_debug']) or
            $config['nette_debug']['email'] = null;

        Debugger::_init();
        
        $config['nette_debug']['bar'] or Debugger::$bar = null;
        
        Debugger::enable(
            $config['nette_debug']['mode'],
            $config['nette_debug']['log'],
            $config['nette_debug']['email']
        );

        !array_key_exists('strict', $config['nette_debug']) or
            Debugger::$strictMode = $config['nette_debug']['strict'];

        !array_key_exists('max_depth', $config['nette_debug']) or
            Debugger::$maxDepth = $config['nette_debug']['max_depth'];

        !array_key_exists('max_len', $config['nette_debug']) or
            Debugger::$maxLen = $config['nette_debug']['max_len'];

        !array_key_exists('template_map', $config['nette_debug']) or
            $app->getServiceManager()->get('viewtemplatemapresolver')->merge(
                $config['nette_debug']['template_map']
            );
    }

    /**
     * Module default config
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Default autoloader config
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'Nette' => __DIR__ . '/src/Nette',
                ),
            ),
        );
    }
}
