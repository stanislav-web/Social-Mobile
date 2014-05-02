<?php
namespace Plugins\Model; // инициализирую текущее пространство имен

// подключаю адаптеры Бд
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql; // для запросов
// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Модель для записи статистики
 * $sm->get('statistics.Model');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Plugins/src/Plugins/Model/StatisticsModel.php
 */
class StatisticsModel extends AbstractTableGateway implements ServiceLocatorAwareInterface
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
    protected $table    = 'zf_plugin_statistics_attendance';

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
     * getToday() Посещаемость за сегодня
     * @access public
     * @return object DB
     */
    public function getToday()
    {
        $sql	    = "SELECT * FROM `{$this->table}` WHERE `date` >= CURDATE()";
        $Adapter    = $this->adapter;
	
	$result = $Adapter->query($sql, $Adapter::QUERY_MODE_EXECUTE);
	if(!empty($result)) return $result;
	else return null;
    }
    
    /**
     * getYesterday() Посещаемость за вчера
     * @access public
     * @return object DB
     */
    public function getYesterday()
    {
        $sql	    = "SELECT * FROM `{$this->table}` WHERE `date` >= (CURDATE()-1) AND `date` < CURDATE()";
        $Adapter    = $this->adapter;
	
	$result = $Adapter->query($sql, $Adapter::QUERY_MODE_EXECUTE);
	if(!empty($result)) return $result;
	else return null;	
    }
    
    /**
     * getBeforeYesterday() Посещаемость за позавчера
     * @access public
     * @return object DB
     */
    public function getBeforeYesterday()
    {
        $sql	    = "SELECT * FROM `{$this->table}` WHERE `date` >= (CURDATE()-2) AND `date` < (CURDATE()-1)";
        $Adapter    = $this->adapter;
	
	$result = $Adapter->query($sql, $Adapter::QUERY_MODE_EXECUTE);
	if(!empty($result)) return $result;
	else return null;	
    }
   
    /**
     * getWeek() Посещаемость за неделю
     * @access public
     * @return object DB
     */
    public function getWeek()
    {
        $sql	    = "SELECT * FROM `{$this->table}` WHERE `date` >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)";
        $Adapter    = $this->adapter;
	
	$result = $Adapter->query($sql, $Adapter::QUERY_MODE_EXECUTE);
	if(!empty($result)) return $result;
	else return null;	
    }    
    
    /**
     * getMonth() Посещаемость за месяц (30 дней)
     * @access public
     * @return object DB
     */
    public function getMonth()
    {
        $sql	    = "SELECT * FROM `{$this->table}` WHERE `date` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)";
        $Adapter    = $this->adapter;
	
	$result = $Adapter->query($sql, $Adapter::QUERY_MODE_EXECUTE);
	if(!empty($result)) return $result;
	else return null;	
    }     
    
    /**
     * getYear() Посещаемость за год
     * @access public
     * @return object DB
     */
    public function getYear()
    {
        $sql	    = "SELECT * FROM `{$this->table}` WHERE `date` >= DATE_SUB(CURRENT_DATE, INTERVAL 365 DAY)";
        $Adapter    = $this->adapter;
	
	$result = $Adapter->query($sql, $Adapter::QUERY_MODE_EXECUTE);
	if(!empty($result)) return $result;
	else return null;	
    }     
        
    /**
     * set() Установка записи в бд
     * @access public
     * @param \Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent
     * @return object DB initialize
     */
    public function set()
    {
        $service = $this->getServiceLocator()->get('plugins.Service');
        foreach($service->getPlugins() as $value)
        {
            if($this->table == $value['system'])
            {
                // Записую в БД
                $request              = $this->getServiceLocator()->get('request');

                $REMOTE_ADDR        = $request->getServer('REMOTE_ADDR');
                $REQUEST_URI        = $request->getServer('REQUEST_URI');
                $HTTP_USER_AGENT    = $request->getServer('HTTP_USER_AGENT');
                $HTTP_REFERER       = $request->getServer('HTTP_REFERER');

                $Adapter = $this->adapter;
                $sql = new Sql($Adapter);
                $insert = $sql->insert($this->table);
                $data = array(
                    'ip'            => new \Zend\Db\Sql\Expression("INET_ATON('".$REMOTE_ADDR."')"),
                    'page'          => (isset($REQUEST_URI)) ? $REQUEST_URI : '',
                    'agent'         => (isset($HTTP_USER_AGENT)) ? $HTTP_USER_AGENT : '',
                    'referer'       => (isset($HTTP_REFERER)) ? $HTTP_REFERER : '',
                );
                $insert->values($data);
                $selectString = $sql->getSqlStringForSqlObject($insert);
                //print $selectString; exit;
                $resultSet = $this->adapter->query($selectString, $Adapter::QUERY_MODE_EXECUTE);
                break;
            }
        }
        return isset($resultSet) ? $resultSet : '';
    }
}