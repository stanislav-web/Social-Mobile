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
 * Модель флэш стены
 * $sm->get('flashWall.Model');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Plugins/src/Plugins/Model/FlashWallModel.php
 */
class FlashWallModel extends AbstractTableGateway implements ServiceLocatorAwareInterface
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
    protected $table    = 'zf_flash_wall';

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
        $lng        = $this->getServiceLocator()->get('MvcTranslator');     // Переводчик
        $user       = $this->getServiceLocator()->get('user.Model');        // Модель доступа к users
        foreach($service->getPlugins() as $value)
        {
            // Тут немного иначе, чем в других плагинах
            // Я не использую базу данных для выборки меню
            // У меня статический контент, но проверку на активацию плагина сделсть нужно
            if($this->table == $value['system'])
            {
                $resultSet = $this->select(function (Select $select) {
                    $select
                        ->columns(array(
                        'id',
                        'message',
                        'user_id',
                        'date',
                    ))
                    ->order('date DESC');
                    //print $select->getSqlString($this->tableGateway->getPlatform()); // Показую запрос
                });
                $return = $resultSet->toArray();
                if(empty($return)) $return = $lng->translate('There are nobody wrote','plugins');
                else
                {
                    // Раскладываю для слияния
                    foreach($return as $k => $v)
                    {
                        $return[$k]['user_id'] = $user->getProfile($v['user_id']);
                    }
                }
                break;
            }
        } 
        return (!isset($return)) ? '' : $return;
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