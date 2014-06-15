<?php
namespace Social\Model; // инициализирую текущее пространство имен

// подключаю адаптеры Бд
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql; // для запросов
use Zend\Db\Sql\Predicate;

// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
/**
 * Модель определения онлайн посетителей
 * использовать сервис менеджер в модели
 * $sm->get('online.Model');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Model/OnlineModel.php
 */
class OnlineModel extends AbstractTableGateway implements ServiceLocatorAwareInterface
{

    /**
     * Таблица, к которой обращаюсь
     * @access protected
     * @var string $table;
     */
    protected $table = 'zf_users_online';
    
    /**
     * Сколько секунд считать объект в онлайне
     * @access protected
     * @var int $timeon;
     */
    protected $timeon = 300;
    
    /**
     * Зависимые таблицы
     * @access protected
     * @var string $relationsTable;
     */
    protected $relationsTable = array(
        'profile'       =>  'zf_users_profile',     // профиль
        'countries'     =>  'zf_countries',         // страны
        'regions'       =>  'zf_regions',           // регионы
        'cities'        =>  'zf_cities',            // города
    );

    /**
     * $_serviceLocator Свойство для хрения сервис менеджера
     * @access protected
     * @var type object
     */
    protected $_serviceLocator;
    
    /**
     * $_lng Свойство объекта Zend l18 translator
     * @access protected
     * @var type object
     */
    protected $_lng;
    
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
     * insertItem() Установка записи в бд
     * @param int $user_id ID пользователя
     * @param string $title Заголовок страницы
     * @access public
     * @param \Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent
     * @return object DB initialize
     */
    public function insertItem($user_id, $title)
    {
        // Получаю входящие параметры
        
        $request            = $this->getServiceLocator()->get('request');
        $REMOTE_ADDR        = $request->getServer('REMOTE_ADDR');
        $REQUEST_URI        = $request->getServer('REQUEST_URI');   
        
        // Вношу их в таблицу $this->table
        
        $Adapter = $this->adapter;
        $sql = new Sql($Adapter);
        $insert = $sql->insert($this->table);
        $data = array(
            'ip'            => new \Zend\Db\Sql\Expression("INET_ATON('".$REMOTE_ADDR."')"),
            'timestamp'     => time(),
            'page'          => (isset($REQUEST_URI)) ? $REQUEST_URI : '',
            'title'         => $title,
            'user_id'       => $user_id,
        );
        $insert->values($data);
        $selectString = $sql->getSqlStringForSqlObject($insert);
        //print $insert->getSqlString($this->adapter->getPlatform()); // SHOW SQL

        try
        {
            $resultSet = $this->adapter->query($selectString, $Adapter::QUERY_MODE_EXECUTE);                
            return true;
        }
        catch (\Exception $e)
        {
            die('Error: ' . $e->getMessage());
        }         
    }    
    
    /**
     * deleteItems() Удаление устаревших записей онлайн
     * или удаление уже с текущим IP
     * @access public
     * @return object Базы данных
     */
    public function deleteItems()
    {
        // Записую в БД
        $request        = $this->getServiceLocator()->get('request');
        $REMOTE_ADDR    = $request->getServer('REMOTE_ADDR');       

        $sql = new Sql($this->adapter); // Загружаю адаптер БД
        $delete = $sql->delete();
        $delete->from($this->table);
        
        $delete->where(
                array(
                    new Predicate\PredicateSet(
                        array(
                            new Predicate\Operator(
                                'timestamp',                                                // первое поле
                                Predicate\Operator::OPERATOR_LESS_THAN,                     // меньше чем
                                new \Zend\Db\Sql\Expression("`timestamp`+".$this->timeon)  // сумма первого поля + значение
                            ),
                            new \Zend\Db\Sql\Predicate\Operator(
                                'ip',                                                       // второе поле
                                Predicate\Operator::OPERATOR_EQUAL_TO,                      // эквивалентно
                                new \Zend\Db\Sql\Expression("INET_NTOA('".$REMOTE_ADDR."')")// этому выражению
                            ),
                         ),
                     Predicate\PredicateSet::COMBINED_BY_OR                                  //  комбинирую условием OR
                     )
                )
        );
        
        $statement = $sql->prepareStatementForSqlObject($delete);
        //print $delete->getSqlString($this->adapter->getPlatform()); exit();// SHOW SQL

        try 
        {
            $result = $statement->execute();
            return true;
        }
        catch (\Exception $e) 
        {
            die('Error: ' . $e->getMessage());
        }
    }     
    
    /**
     * getAll(Select $select = null)  Выборка всех записей из таблицы
     * @param \Zend\Db\Sql\Select $select
     * @return object DB
     */
    public function getAll($page, $perpage = 10) 
    {
        $online = array(); // массив с результатом
        $this->_lng  = $this->getServiceLocator()->get('MvcTranslator'); // загружаю переводчик
        $result = $this->select(function (Select $select) use ($page, $perpage) {
             $select
                    ->quantifier(new \Zend\Db\Sql\Expression('SQL_CALC_FOUND_ROWS'))
                    ->columns(array(
                        'ip',
                        'timestamp',
                        'page',
                        'title',
                    ))
                    ->join($this->relationsTable['profile'], $this->relationsTable['profile'].'.user_id = '.$this->table.'.user_id',
                        array(
                            'name',
                            'gender',
                            'photo',
                            'name',
                            'country_id',
                            'region_id',
                            'city_id',
                            'personal',
                            'user_id',
                            'birthday',
                            'timezone'
                        )
                    )
                    ->join($this->relationsTable['cities'], $this->relationsTable['cities'].'.city_id = '.$this->relationsTable['profile'].'.city_id', 
                        array(
                            'city'  => 'city_'.substr($this->_lng->getLocale(), 0,2)
                        )
                    )
                    ->join($this->relationsTable['countries'], $this->relationsTable['countries'].'.country_id = '.$this->relationsTable['profile'].'.country_id', 
                        array(
                            'country_code',
                            'country'       => 'country_'.substr($this->_lng->getLocale(), 0,2)
                        )
                    )
                    ->join($this->relationsTable['regions'], $this->relationsTable['regions'].'.region_id = '.$this->relationsTable['profile'].'.region_id', 
                            array(
                                'region'  => 'region_'.substr($this->_lng->getLocale(), 0,2)
                            )
                    )                      
                    ->order($this->table.'.timestamp DESC')
                    ->offset(($page - 1) * $perpage)
                    ->limit($perpage);                     
        });
        
        /* получаю sql объект из TableGateway */
        
        $sql = $this->getSql();
        
        /* 
         * Создаю пустой объект Select на случай если результатов не будет вообще из выборки
         */
        
        $select = new Select(' ');
        
        /* 
         * Обновляю предыдущий Select добавляя в него новое поле
         */
        
        $select->setSpecification(Select::SELECT, array(
            'SELECT %1$s' => array(
                array(1 => '%1$s', 2 => '%1$s AS %2$s', 'combinedby' => ', '),
                null
            )
        ));
        
        $select->columns(array(
            'total' => new \Zend\Db\Sql\Expression("FOUND_ROWS()")
        ));
        
        /* 
         * Выполняю SQL и получаю результат для выдачи в контроллер
         */
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result2 = $statement->execute();
        $row = $result2->current();
        $total = $row['total'];
                
        if($result)
        {
            return $event['items'] = array(
                'total' => $total,
                'online' => $result->toArray(),
                
            );
        }
        else return null;
    }    
}