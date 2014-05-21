<?php
namespace Cronjob\Model; // инициализирую текущее пространство имен

// подключаю адаптеры Бд
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql; // для запросов
// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Модель фиксации работы планировшика Cron
 * $sm->get('cronjobLog.Model');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Cronjob/src/Cronjob/Model/CronjobLogModel.php
 */
class CronjobLogModel extends AbstractTableGateway implements ServiceLocatorAwareInterface
{
    /**
     * $_serviceLocator Свойство для хрения сервис менеджера
     * @access protected
     * @var type object
     */
    protected $_serviceLocator;

    /**
     * Таблица, к которой обращаюсь
     * @access protected
     * @var string $table;
     */
    protected $table    = 'zf_cronjob_log';

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
     * write($data) Логирование в БД
     * @access public
     * @param \Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent
     * @return object DB initialize
     */
    public function write($data)
    {
        $Adapter = $this->adapter;
        $sql = new Sql($Adapter);
        $insert = $sql->insert($this->table);
        $data = array(
            'message'           => $data['message'],
            'command'           => $data['command'],
            'sheduler'          => $data['sheduler'],
        );
        $insert->values($data);
        $selectString = $sql->getSqlStringForSqlObject($insert);
        $resultSet = $this->adapter->query($selectString, $Adapter::QUERY_MODE_EXECUTE);
        if(!$resultSet)throw new \RuntimeException('Cronjob command write '.$data['command'].' failure!');
    }
}