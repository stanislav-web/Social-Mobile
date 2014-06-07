<?php
namespace Submissions; // declare namespace for the current module "Submissions"

/**
 * Module for the Payments
 * @package Zend Framework 2
 * @subpackage Submissions
 * @since PHP >=5.4
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Submissions/Module.php
 */
class Module
{
    /**
     * getConfig() configurator boot method for application
     * @access public
     * @return file
     */
    public function getConfig()
    {
        return include __DIR__.'/config/module.config.php';
    }
    
    /**
     * getServiceConfig() Load module services
     * @access public
     * @return array
     */
    public function getServiceConfig()
    {
        return array(
            'service_manager' => array(
                
                // Set Provider factory
                
                'factoires' => array(
                    'Submissions\Factory\ProviderFactory'  => __NAMESPACE__.'\Factory\ProviderFactory',
                ),
            ),            
        );
    }    
    
    /**
     * getAutoloaderConfig() installation method autoloaders 
     * In my case, I connect the class map 
     * And set the namespace for the MVC application directory
     * @access public
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            
            // install namespace for MVC Module directory
            'Zend\Loader\StandardAutoloader'    =>  array(
                'namespaces'    =>  array(
                    __NAMESPACE__               =>  __DIR__.'/src/'.__NAMESPACE__,
                ),
            ),
            
            // add classmap file. Be careful! Update this map when adding a new provider!
            'Zend\Loader\ClassMapAutoloader'    =>  array(
                __DIR__.'/autoload_classmap.php',
            ),
            
        );        
    } 
}
