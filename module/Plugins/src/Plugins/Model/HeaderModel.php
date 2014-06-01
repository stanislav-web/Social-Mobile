<?php
namespace Plugins\Model; // инициализирую текущее пространство имен

// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Модель вывода QR Кода в шаблоне
 * $sm->get('QRCode.Model');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Plugins/src/Plugins/Model/HeaderModel.php
 */
class HeaderModel implements ServiceLocatorAwareInterface
{
    /**
     * $_serviceLocator Свойство для хрения сервис менеджера
     * @access protected
     * @var type object
     */
    protected $_serviceLocator;

    /**
     * Системный код плагина
     * @access protected
     * @var string $table;
     */
    protected $table    = 'header'; 
    
    /**
     * setServiceLocator(ServiceLocatorInterface $_serviceLocator) Метод реализующий интерфейс установки сервис локатора
     * @access public
     * @var object
     */
    public function setServiceLocator(ServiceLocatorInterface $_serviceLocator)
    {
        $this->_serviceLocator = $_serviceLocator;
    }

    /**
     * getServiceLocator(ServiceLocatorInterface $_serviceLocator) Метод реализующий интерфейс загрузки сервис локатора
     * @access public
     * @var object
     */
    public function getServiceLocator()
    {
        return $this->_serviceLocator;
    }

    /**
     * get() Метод выдает уведомления по коду
     * @access public
     * @param \Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent
     * @return object DB initialize
     */
    
    public function get()
    {
        $service    = $this->getServiceLocator()->get('plugins.Service');   // Мой менеджер плагинов
        foreach($service->getActivePlugins() as $value)
        {
            if($this->table == $value['system'])
            {
                $result = true; // просто вот так вот ))
                break;
            }
        } 
        return (!isset($result)) ? false : true; // возвращаю в сервис плагинов
    }
}