<?php
namespace Plugins\Model; // инициализирую текущее пространство имен

// подключаю адаптеры Бд
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Модель управения сервисами социальных закладок
 * $sm->get('bookmarks.Model');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Plugins/src/Plugins/Model/BookmarksModel.php
 */
class BookmarksModel implements ServiceLocatorAwareInterface
{
    /**
     * $__authService Свойство хранения сервиса менеджера
     * @access protected
     * @var type object
     */
    protected $_serviceLocator;

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
     * Таблица, к которой обращаюсь
     * @access protected
     * @var string $table;
     */
    protected $table    = 'zf_plugin_bookmarks';

    /**
     * Шлюз БД
     * @access protected
     * @var object $tableGateway;
     */
    protected $tableGateway;

    /**
     * Конструктор адаптера БД
     * @access public
     * @param \Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent
     * @return object DB initialize
     */
    public function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }

    /**
     * get() Конфигурации социальных закладок
     * @access public
     * @param \Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent
     * @return object DB initialize
     */
    public function get()
    {
        $service = $this->getServiceLocator()->get('plugins.Service');
        foreach($service->getPlugins() as $value)
        {
            if($this->table == $value['system'])
            {
                $resultSet = $this->tableGateway->select()->toArray();
                break;
            }
        }
        return isset($resultSet) ? $resultSet : '';
    }
}