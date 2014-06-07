<?php
namespace Social\Model; // инициализирую текущее пространство имен

// подключаю адаптеры Бд
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql; // для запросов INSERT
// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Модель событий пользователя
 * использовать сервис менеджер в модели
 * $sm->get('events.Model');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Model/EventsUserModel.php
 */
class EventsUserModel extends AbstractTableGateway implements ServiceLocatorAwareInterface
{

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
    protected $table = 'zf_users_events';

    /**
     * Конструктор адаптера БД
     * @access public
     * @param \Zend\Db\TableGateway\Feature\EventFeature\AbstractTableGatewayEvent
     * @return object DB initialize
     */
    public function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
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
     * setEventForRegister($event_system, $user = array()) Событие на нового пользователя в регистрации
     * @param string $event_system системное имя события
     * @param array $user массив с данными пользователя
     * @access public
     * @return object Базы данных
     */
    public function setEventForRegister($event_system, $user = array())
    {
        $config     = $this->zfService()->get('events.Model')->get($event_system);  // общие настройки событий

        /**
         * Устанавливаю событие в таблицу для пользователей $table = 'zf_users_events';
         */
            
         $Adapter = $this->adapter;
         $sql = new Sql($Adapter);
         $insert = $sql->insert($this->table);
         $dataEvent = array(
             'subject_ru'    => sprintf($config->subject_ru, $user['title']['ru']),
             'subject_en'    => sprintf($config->subject_en, $user['title']['en']),
             'subject_ua'    => sprintf($config->subject_ua, $user['title']['ua']),
             'message_ru'    => sprintf($config->message_ru, $user['name'], $user['title']['ru']),
             'message_en'    => sprintf($config->message_en, $user['name'], $user['title']['en']),
             'message_ua'    => sprintf($config->message_ua, $user['name'], $user['title']['ua']),
             'user_id'       => $user['user_id'],
             'date'          => new \Zend\Db\Sql\Expression("NOW()"),
         );
         $insert->values($dataEvent);
         $selectString = $sql->getSqlStringForSqlObject($insert);
         $results = $this->adapter->query($selectString, $Adapter::QUERY_MODE_EXECUTE);
         if(!$results)  throw new \Exception('Ivalid insert parameter. SQL Query is invalid');
         else return true;
    }
    
    /**
     * getEvents($user_id) Все события пользователя
     * @param int $user_id ID пользователя
     * @param int $page - страница текущая
     * @param int $perpage - записей на страницу
     * @return void
     */
    public function getEvents($user_id, $page, $perpage)
    {
        $events = array(); // массив с результатом
        $this->_lng  = $this->zfService()->get('MvcTranslator'); // загружаю переводчик
        
        // Использую лямпду как передаваемый объект для выборки
        $result = $this->select(function (Select $select) use ($user_id, $page, $perpage) {
             $select
                    ->quantifier(new \Zend\Db\Sql\Expression('SQL_CALC_FOUND_ROWS'))
                    ->columns(array(
                        'subject'   =>   'subject_'.substr($this->_lng->getLocale(), 0,2),
                        'message'   =>   'message_'.substr($this->_lng->getLocale(), 0,2),
                        'date',
                        'read',
                    ))
                    ->where('`user_id` = '.(int)$user_id)
                    ->order('id DESC')
                    ->limit($perpage)
                    ->offset(($page - 1) * $perpage);                     
        });
        
        /* получаю sql объект из TableGateway */
        
        $sql = $this->getSql();
        
        /* 
         * create an empty select statement passing in some random non-empty string as the table.
         * Need this because Zend select statement will
         * generate an empty SQL if the table is empty.
         */
        
        $select = new Select(' ');
        
        /* 
         * update the select statement specification
         * so that we don't incorporate the FROM clause
         */
        
        $select->setSpecification(Select::SELECT, array(
            'SELECT %1$s' => array(
                array(1 => '%1$s', 2 => '%1$s AS %2$s', 'combinedby' => ', '),
                null
            )
        ));
        
        /* 
         * specify the column 
         */
        $select->columns(array(
            'total' => new \Zend\Db\Sql\Expression("FOUND_ROWS()")
        ));
        
        /* 
         * execute the select and extract the total 
         */
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result2 = $statement->execute();
        $row = $result2->current();
        $total = $row['total'];
        
        /* TODO: need to do something with the total? */
        
        if($result)
        {
            return $event['items'] = array(
                'total' => $total,
                'events' => $result->toArray(),
                
            );
        }
        else return null;
    }
}