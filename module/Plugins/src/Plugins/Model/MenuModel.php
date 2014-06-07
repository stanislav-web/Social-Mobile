<?php
namespace Plugins\Model; // инициализирую текущее пространство имен

// подключаю адаптеры Бд
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Модель управения всеми меню сайта
 * $sm->get('notices.Model');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Plugins/src/Plugins/Model/MenuModel.php
 */
class MenuModel implements ServiceLocatorAwareInterface
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
    protected $table    = 'zf_plugin_menu';

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
     * get($system) Метод выдает уведомления по коду
     * @param string $system Системный код уведомления
     * @access public
     * @param \Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent
     * @return object DB initialize
     */
    public function get($system)
    {
        $service = $this->getServiceLocator()->get('plugins.Service');
        foreach($service->getActivePlugins() as $value)
        {
            // Тут немного иначе, чем в других плагинах
            // Я не использую базу данных для выборки меню
            // У меня статический контент, но проверку на активацию плагина сделсть нужно
            if($this->table == $value['system'])
            {
                $resultSet = $this->tableGateway->select(function (Select $select) use($system) {
                    $select
                        ->columns(array(
                        'title'
                    ))
                    ->where('`system` = \''.$system.'\' AND `activation` =\'1\'');
                    //$select->getSqlString($this->tableGateway->getPlatform()); // Показую запрос
                })->current();
                break;
            }
        }
        return (isset($resultSet)) ? $resultSet : null;
    }
}