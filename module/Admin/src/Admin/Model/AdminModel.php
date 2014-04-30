<?php
namespace Admin\Model; // инициализирую текущее пространство имен

// подключаю адаптеры Бд
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Модель для административной панели
 * использовать сервис менеджер в модели
 * $sm->get('admin.Model');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Model/AdminModel.php
 */
class AdminModel implements ServiceLocatorAwareInterface
{
    /**
     * $_adapter Адаптер БД
     * @access protected
     * @var type object
     */    
    protected $adapter = null;
    
    /**
     * $_serviceLocator Свойство для хрения сервис менеджера
     * @access protected
     * @var type object
     */
    protected $_serviceLocator;

    /**
     * Основная таблица, к которой обращаюсь
     * @access protected
     * @var string $table;
     */
    protected $table = 'zf_users';

    /**
     * Конструктор адаптера БД
     * @access public
     * @param \Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent
     * @return object DB initialize
     */
    public function __construct($adapter)
    {
        $this->_adapter = new TableGateway($this->table, $adapter);        
    }

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
     * zfService() Менеджер зарегистрированных сервисов ZF2
     * @access public
     * @return ServiceManager
     */
    public function zfService()
    {
        return $this->getServiceLocator();
    }

    /**
     * isAdmin($id) Проверка админа
     * @param string $login
     * @access public
     * @return object DB `zf_users`
     */
    public function isAdmin($id)
    {
        // Использую лямпду как передаваемый объект для выборки
        $resultSet = $this->adapter->select(function (Select $select) use ($id) {
            $select
                ->where('`activation` = \'1\' AND `group_id` = \'1\' AND `id` = \''.$id.'\'')
                ->limit(1);
               //$select->getSqlString($this->_adapter->getPlatform()); // SHOW SQL
        })->current();
        if($resultSet) return true;
        else return false;
    }
}