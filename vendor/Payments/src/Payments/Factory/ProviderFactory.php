<?php
namespace Payments\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Payments\Exception;

/**
 * Use this factory for get some payment provider
 * @package Zend Framework 2
 * @subpackage Payments
 * @since PHP >=5.4
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Payments/src/Payments/Factory/ProviderFactory.php
 */
class ProviderFactory implements FactoryInterface, ServiceLocatorAwareInterface {

    /**
     * $__provider may be able from the \Payments\Provider
     * @access private
     * @var array 
     */
    private $__provider = null;

    /**
     * $__serviceLocator Service Locator for create implemented object
     * @access private
     * @var object 
     */  
    private $__serviceLocator;
    
    /**
     * createService(ServiceLocatorInterface $serviceLocator) Create object method
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @access public
     * @return \Payments\Factory\ProviderFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->__serviceLocator = $serviceLocator;
        return $this;
    } 
    
    /**
     * setServiceLocator(ServiceLocatorInterface $serviceLocator) Implement ServiceLocator
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @access public
     * @return null
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->__serviceLocator = $serviceLocator;
    }

    /**
     * getServiceLocator()
     * @access public
     * @return object ServiceLocator
     */
    public function getServiceLocator()
    {
        return $this->__serviceLocator;
    }    
    
    /**
     * getProvider($provider) Get produced provider object
     * @param string $provider produced object
     * @return object \Payments\Provider
     * @throws Exception\ExceptionStrategy
     */
    public function getProvider($provider)
    {
        // need to provide dynamic objects creations 
        
        if(null === $this->__provider) 
        {
            $object = "\\Payments\\Provider\\$provider";
            // checking class..
            if(!class_exists($object)) throw new Exception\ExceptionStrategy($provider.' provider does not exist');
            $this->__provider    =  new $object($this->__serviceLocator);
        }
        return $this->__provider;
    }
    
    /**
     * getProviders() Get all produced providers
     * @throws Exception\ExceptionStrategy
     */
    public function getProviders()
    {
        $providers  =   $this->getServiceLocator()->get('Config')["Payments\Provider\Config"];
        if(!$providers || empty($providers))  throw new Exception\ExceptionStrategy('Providers does not exist');
        return $providers;
    }    
}