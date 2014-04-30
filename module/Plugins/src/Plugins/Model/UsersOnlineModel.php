<?php
namespace Plugins\Model; // инициализирую текущее пространство имен

// подключаю адаптеры Бд
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql; // для запросов
use Zend\Db\Sql\Expression;
// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Модель вывода информера пользоватеей онлайн
 * $sm->get('usersOnline.Model');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Plugins/src/Plugins/Model/UsersOnlineModel.php
 */
class UsersOnlineModel extends AbstractTableGateway implements ServiceLocatorAwareInterface
{
    /**
     * $_serviceLocator Свойство для хрения сервис менеджера
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
    protected $table    = 'zf_users_profile';

    /**
     * Конструктор адаптера БД
     * @access public
     * @param \Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent
     * @return object DB initialize
     */
    public function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    /**
     * get($system) Метод выдает уведомления по коду
     * @param string $system Системный код уведомления
     * @access public
     * @param \Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent
     * @return object DB initialize
     */
    
    public function get()
    {
        $service    = $this->getServiceLocator()->get('plugins.Service');   // Мой менеджер плагинов
        foreach($service->getPlugins() as $value)
        {
            if($this->table == $value['system'])
            {
                /**
                 * Необходимо реализовать такое
                 *  SELECT 
                 *  (SELECT COUNT('id') FROM zf_users_profile WHERE online = '1') AS `all`,
                 *  (SELECT COUNT('id') FROM zf_users_profile WHERE online = '1' AND gender = '1') AS `m`,
	         *  (SELECT COUNT('id') FROM zf_users_profile WHERE online = '1' AND gender = '2') AS `f`
                 *  FROM zf_users_profile LIMIT 1;
                 */
                
                $sql = "SELECT 
			(SELECT COUNT('id') FROM `{$this->table}` WHERE online = '1' AND gender = '1') AS `m`,
                        (SELECT COUNT('id') FROM `{$this->table}` WHERE online = '1' AND gender = '2') AS `f`
                        FROM `{$this->table}` LIMIT 1;";
                
                $Adapter = $this->adapter;
                $result = $Adapter->query($sql, $Adapter::QUERY_MODE_EXECUTE);
                if(!empty($result)) 
                {
                    $result = $result->current();
                    $result->all = $result->f + $result->m;
                }
                break;
            }
        } 
        return (!isset($result)) ? '' : $result;
    }
    
    /**
     * set() Установка записи в бд
     * @param string $message Сообщение
     * @param int $user_id ID пользователя
     * @access public
     * @param \Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent
     * @return object DB initialize
     */
    public function set($message, $user_id)
    {
        $Adapter = $this->adapter;
        $sql = new Sql($Adapter);
        $insert = $sql->insert($this->table);
        $data = array(
            'message'           => $message,
            'user_id'           => $user_id,
            'date'              => new Expression("NOW()"),
        );
        
        $insert->values($data);
        $statement = $sql->prepareStatementForSqlObject($insert);
        
        $rows = 0;
        try {
            $result = $statement->execute();
            $rows = $result->getAffectedRows();
            return $rows;
        } catch (\Exception $e) {
            die('Error: ' . $e->getMessage());
        }        
    }    
}