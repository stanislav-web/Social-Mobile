<?php
namespace Payments\Aware;

/**
 * ProviderInterface. Implementing rules necessary functionality for providers
 * @package Zend Framework 2
 * @subpackage Payments
 * @since PHP >=5.4
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Payments/src/Payments/Aware/ProviderInterface.php
 */
interface ProviderInterface {
    
    /**
     * getConfig() Get provider configurations
     * @access public
     */
    public function getConfig();
    
    /**
     * getForm() Get from required params
     * @access public
     */
    public function getFormParams();
    
    /**
     * getAdapterConfig() Get adapter configuration by default
     * @access public
     */
    public function getAdapterConfig();
    
    
    /**
     * processForm() Process form handler
     * @param \Zend\Http\Request $request
     * @access public
     */
    public function processForm(\Zend\Stdlib\Parameters $parameters);    
}
