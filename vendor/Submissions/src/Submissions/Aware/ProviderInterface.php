<?php
namespace Submissions\Aware;

/**
 * ProviderInterface. Implementing rules necessary functionality for providers
 * @package Zend Framework 2
 * @subpackage Submissions
 * @since PHP >=5.4
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Submissions/src/Submissions/Aware/ProviderInterface.php
 */
interface ProviderInterface {
    
    /**
     * getConfig() Get provider configurations
     * @access public
     */
    public function getConfig();
    
    /**
     * getAdapterConfig() Get adapter configuration by default
     * @access public
     */
    public function getAdapterConfig();
    
    /**
     * sendRequest($uri = null, array $data = null) Send request to server
     * @param string $uri request URL
     * @param array $data request data
     * @access public
     */
    public function sendRequest($uri = null, array $data = null);
}
