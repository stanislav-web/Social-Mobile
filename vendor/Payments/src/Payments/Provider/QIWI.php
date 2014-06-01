<?php
namespace Payments\Provider;

use Payments\Exception;
use Payments\Aware;

// Here should implemented only those libraries that are needed for this provider
use Zend\Http\Client;
use Zend\Debug\Debug;
use Zend\Http\Request;

/**
 * QIWI Provider. Powered by Protocol interaction with providers payment service Visa QIWI Wallet v2.1.
 * @package Zend Framework 2
 * @subpackage Payments
 * @since PHP >=5.4
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Payments/src/Payments/Provider/QIWI.php
 */
class QIWI extends \ReflectionClass implements Aware\ProviderInterface {
    
    /**
     * $_sm Service Manager
     * @access private
     * @var object 
     */      
    private $_sm    =   null;
   
    /**
     * $__config Configuration for this provider (Cannot modify outside)
     * @see getConfig()
     * @access private
     * @var array $config
     */
    private $__config  =   null; 
    
    /**
     * $__adapterConfig Adapter connection settings
     * @see getAdapterConfig()
     * @access private
     * @var array $config
     */
    private $__adapterConfig  =   null;
    
    /**
     * 
     * @param \Zend\ServiceManager\ServiceManager $ServiceManager
     * @return instance of ServiceManager, Configuration
     * 
     */
    public function __construct(\Zend\ServiceManager\ServiceManager $ServiceManager) 
    {
        parent::__construct(__CLASS__);
        
        // set ServiceManager throught constructor
        if(null === $this->_sm)  $this->_sm =   $ServiceManager;
        $this->__config   =   $this->_sm->get('Config')["Payments\Provider\Config"][$this->getShortName()];
    }        
  
    /**
     * getConfig() Get provider configurations
     * @access public
     * @see Aware\ProviderInterface
     * @return array
     * @throws Exception\ExceptionStrategy
     */
    public function getConfig()
    {
        if(empty($this->__config))   throw new Exception\ExceptionStrategy($this->getShortName().' config file is empty');
        return $this->__config;
    }
    
    
    /**
     * getAdapterConfig() Get adapter configuration by default
     * @access public
     * @see Aware\ProviderInterface
     * @return array
     */
    public function getAdapterConfig()
    {
        if(empty($this->__adapterConfig))   throw new Exception\ExceptionStrategy($this->getShortName().' connection adapter is not configured');
        return $this->__adapterConfig;        
    }
    
    
    /**
     * __setAdapterConfig() Set adapter additional configuration
     * @param array $adapterConfig  additional configurations
     * @access private
     * @return array
     */
    private function __setAdapterConfig(array $adapterConfig)
    {
        $this->__adapterConfig  =   [
                'adapter'       => '\Zend\Http\Client\Adapter\Curl',
                'curloptions'   => array_merge($adapterConfig, $this->__config['adapter']),
        ];
    }
    
    /**
     * getFormParams() Get from required params
     * @access public
     * @see Aware\ProviderInterface
     * @return array
     */
    public function getFormParams() 
    {
        return $this->getConfig()['form'];
    }
    
    
    /**
     * billedRequest(array $param) Billed request to the costomer
     * @param array $param 
     * @access public
     * @return json 
     */
    public function processForm(\Zend\Stdlib\Parameters $parameters)
    {

        
        // setup request uri
        $uri = $this->__config['request_uri'].'/api/v2/prv/'.$this->__config['prv_id'].'/bills/'.$this->__config['bill_id'];

        // setup connection params merged with hidden system fields
        
        if(!empty($this->__config['form']['system'])) $parameters = array_merge((array)$parameters, $this->__config['form']['system']);

        $this->__setAdapterConfig([
                'CURLOPT_USERPWD'           =>  $this->__config['prv_id'].":".$this->__config['password'],      // auth param
                'CURLOPT_CUSTOMREQUEST'     =>  'PUT',                                                          // set transaction method
                'CURLOPT_POSTFIELDS'        =>  http_build_query($parameters),                                  // request data                          
        ]);
        
        // building request....
        $request    =   new Request();
        
        $request->setUri($uri);
        $request->setMethod('PUT');
        $client = new Client();

        $client->setAdapter("Zend\Http\Client\Adapter\Curl");
        $client->setOptions($this->getAdapterConfig()['curloptions']);
        
        Debug::dump($client->getUri());
        
        exit;        
        
        $response = $client->dispatch($request);        
        
        
        

        $client = new Client();
        $client->setUri($uri);
        $client->setMethod('PUT');
        $client->setParameterPost($parameters);

        $response = $client->send();

        if ($response->isSuccess()) {
            // the POST was successful
        }        
        exit;
        
        // send following request over CURL client
        $client =   new Client($uri, $this->getAdapterConfig());
        
        Debug::dump($client, 'Client Object');
        $response = $client->send();
        Debug::dump($response, 'Response');
                        exit;
        return $client->getResponse();
    }
}
