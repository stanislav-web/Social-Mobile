<?php
namespace Social\Model; // инициализирую текущее пространство имен

// подключаю адаптеры Бд
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql; // для запросов
use Zend\Db\Sql\Expression;

// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
/**
 * Модель поьзователя
 * использовать сервис менеджер в модели
 * $sm->get('user.Model');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Model/UserModel.php
 */
class UserModel extends  AbstractTableGateway implements ServiceLocatorAwareInterface
{
    /**
     * Таблица, к которой обращаюсь
     * @access protected
     * @var string $table;
     */
    protected $table = 'zf_users';
    
    /**
     * Сколько секунд считать user в онлайне
     * @access protected
     * @var string $table;
     */
    protected $timeon = 300;   
    
    /**
     * Зависимые таблицы
     * @access protected
     * @var string $relationsTable;
     */
    protected $relationsTable = array(
        'profile'   =>  'zf_users_profile',     // профиль
        'group'     =>  'zf_users_roles',       // группы
        'status'    =>  'zf_users_statuses',    // коммерческие статусы
        'events'    =>  'zf_users_events',      // журнал (события)
        'countries' =>  'zf_countries',		// страны
        'regions'   =>  'zf_regions',		// регионы
        'cities'    =>  'zf_cities',		// города
        'online'    =>  'zf_users_online',	// кто где в онлайн?
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
     * zfService() Менеджер зарегистрированных сервисов ZF2
     * @access public
     * @return ServiceManager
     */
    public function zfService()
    {
        return $this->getServiceLocator();
    }
    
    /**
     * getUsers($page, $perpage, $filter = null) Все пользователи (с фильтром)
     * @param int $page - страница текущая
     * @param int $perpage - записей на страницу
     * @param string $order - сортировка
     * @param array $filter - записей на страницумассив с объектами фильтрации
     * @return void
     */
    public function getUsers()
    {
        
        $this->_lng  = $this->zfService()->get('MvcTranslator'); // загружаю переводчик
        
        $users = array(); // массив с результатом
        
        // Использую лямпду как передаваемый объект для выборки
        $result = $this->select(function (Select $select) {
        $select
            ->join($this->relationsTable['profile'], $this->relationsTable['profile'].'.user_id = '.$this->table.'.id', array(
		'name',
		'gender',
		'email',
		'photo',
		'birthday',
		'phone',
                'country_id',                    
                'region_id',                    
                'city_id',                    
                'interests',                    
                'length',                    
                'weight',                    
                'about',                    
                'alias',                    
                'personal',
                'timezone',                    
                ), $select::JOIN_LEFT)
            ->join($this->relationsTable['group'], $this->relationsTable['group'].'.id = '.$this->table.'.role_id', array(
                'group_title' => 'title_'.substr($this->_lng->getLocale(), 0,2),
            ), $select::JOIN_LEFT)
            ->join($this->relationsTable['status'], $this->relationsTable['status'].'.id = '.$this->table.'.status_id', array(
                'status_title' => 'title',
            ), $select::JOIN_LEFT)
            ->join($this->relationsTable['countries'], $this->relationsTable['countries'].'.country_id = '.$this->relationsTable['profile'].'.country_id', array(
                'country_code',
		'country'       => 'country_'.substr($this->_lng->getLocale(), 0,2),
            ))
	    ->join($this->relationsTable['regions'], $this->relationsTable['regions'].'.region_id = zf_users_profile.region_id', array(
                    'region'  => 'region_'.substr($this->_lng->getLocale(), 0,2)
            ))
            ->join($this->relationsTable['cities'], $this->relationsTable['cities'].'.city_id = zf_users_profile.city_id', array(
                    'city'  => 'city_'.substr($this->_lng->getLocale(), 0,2)
            ))    		
            ->order('id ASC');                     
        });
        $result->buffer();
        $result->next();
        
        if($result) return $result;
        else return null;
    }    
    
    /**
     * get() Метод выдает уведомления по коду (для сервиса Плагинов)
     * @access public
     * @param \Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent
     * @return object DB initialize
     */
    public function get()
    {
        // Необходимо получить количество пользователей в онлайне по половой принадлежгости
        $service    = $this->getServiceLocator()->get('plugins.Service');   // Мой менеджер плагинов
        foreach($service->getActivePlugins() as $value)
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
                
                $sql = "
                    SELECT 
                    SUM(IF({$this->relationsTable["profile"]}.gender =  '1', 1, 0)) as `m`,
                    SUM(IF({$this->relationsTable["profile"]}.gender =  '2', 1, 0)) as `f`
                    FROM {$this->relationsTable["online"]} 
                    INNER JOIN {$this->relationsTable["profile"]}
                    WHERE {$this->relationsTable["profile"]}.user_id = {$this->relationsTable["online"]}.user_id LIMIT 1";

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
     * checkRole($user_id, $role_id) Проверка ролей
     * @param int $user_id ID пользователя
     * @param int $role_id ID роли
     * @access public
     * @return object DB `zf_users`
     */
    public function checkRole($user_id, $role_id)
    {
        // Использую лямпду как передаваемый объект для выборки
        $resultSet = $this->select(function (Select $select) use ($user_id, $role_id) {
            $select
                    ->columns(array(
                    'id'
                ))
                ->where('`id` = '.(int)$user_id.' AND `role_id` = '.(int)$role_id.' AND `activation` = \'1\'')
                ->limit(1);
               //$select->getSqlString($this->_adapter->getPlatform()); // SHOW SQL
        })->current();
        if($resultSet) return true;
        else return false;
    }
    
    /**
     * getAccess($id) Получаю идентификатор доступа
     * @param int $id ID в БД
     * @access public
     * @return int ID пользователя 
     */
    public function getAccess($id)
    {
        $resultSet = $this->select(function (Select $select) use ($id) {
            $select
                    ->columns(array(
                    'group_id'
                ))
                ->where('`'.$this->table.'`.`id` = '.(int)$id.' AND `activation` = \'1\'')
                ->limit(1);
            //print $select->getSqlString($this->adapter->getPlatform()); // SHOW SQL
        })->current();
        return $resultSet;
    }    
    
    /**
     * getID($login) Выбираю ID пользователя по логину
     * @param int $login Логин в БД
     * @access public
     * @return int ID пользователя 
     */
    public function getID($login)
    {
        $resultSet = $this->select(function (Select $select) use ($login) {
            $select
                    ->columns(array(
                    'id'
                ))
                ->where('`'.$this->table.'`.`login` = \''.$login.'\' AND `activation` = \'1\'')
                ->limit(1);
            //print $select->getSqlString($this->adapter->getPlatform()); // SHOW SQL
        })->current();

        return (!empty($resultSet)) ? $resultSet->id : null;
    }
    
    /**
     * getUser($id) Выбираю пользователя по ID
     * @param int $id ID в БД
     * @access public
     * @return int ID пользователя 
     */
    public function getUser($id)
    {
        $resultSet = $this->select(function (Select $select) use ($id) {
            $select->where('`'.$this->table.'`.`id` = '.(int)$id)
                ->limit(1);
            //print $select->getSqlString($this->adapter->getPlatform()); // SHOW SQL
        })->current();

        return $resultSet;
    }    
    
    
     /**
     * countAll() подсчет всех зарегистрированных пользователей
     * @access public
     * @return object Базы данных
     */
    public function countUsers($gender = null)
    {
        //$resultSet = $this->select()->count();
        if($gender) $gender = 'AND `gender` = \''.$gender.'\'';
        $resultSet = $this->select(function (Select $select) use ($gender) {
            $select
                    ->columns(
                                array(
                                    'count' => new \Zend\Db\Sql\Expression('COUNT('.$this->table.'.id)')
                                    )
                            )
                    ->join('zf_users_profile', 'zf_users_profile.user_id = '.$this->table.'.id', array(
                    ))
                    ->where('`activation` = \'1\' '.$gender.'')->limit(1);
        })->current();
        //$select->getSqlString($this->adapter->getPlatform()); // SHOW SQL
        return $resultSet;
    }
 
    /**
     * setTimeOnline($status, $user) Обновление статуса в сети (не в сети)
     * @param enum $status статус (1 - в сети, 0 - не в сети)
     * @param object $user пользователь если есть
     * @access public
     * @return object Базы данных
     */
    public function setTimeOnline($user)
    {
        $Adapter = $this->adapter; // Загружаю адаптер БД
        $sql = new Sql($Adapter);
        $update     = $sql->update($this->table);

        // Обновляю пользователя в онлайне
        $time           = time();
        $updateArray    = array();
        $convert        = new \SW\String\Format();

        $date_lastvisit   = $convert->datetimeToTimestamp($user->date_lastvisit);
            
        // Делаю подсчет веремени в онлайне в сек.
        if($time < $date_lastvisit+$this->timeon) // если текущее время меньше установленного значения в сумме с датой посл. визита
        {
            // считаю разницу от текущего времени до последнего визита            
            $useronline = ($time - $date_lastvisit); 
            // если эта разница меньше допустимого в онлайн, то он еще на сайте
            if($useronline < $this->timeon) $updateArray['time_online'] = $user->time_online+$useronline;
        }
        $updateArray['date_lastvisit']  = $convert->timestampToDatetime($time);
        
        $update->set($updateArray);
        $update->where(array('id' => $user->id));
        $statement = $sql->prepareStatementForSqlObject($update);
        //print $update->getSqlString($this->adapter->getPlatform()); // SHOW SQL

        $rows = 0;
        try {
            $result = $statement->execute();
            $rows = $result->getAffectedRows();
            return $rows;
        } catch (\Exception $e) {
            die(
                    'Error: '.$e->getMessage().'<br>
                     Query: '.$update->getSqlString($this->adapter->getPlatform())
               );
        } 
    }    

    /**
     * lastUsers($limit = 5) метод выборки последних активных
     * а также зарегистрированных пользователей
     * @param int $limit количество пользователей по умолчанию
     * @access public
     * @return object Базы данных
     */
    public function lastUsers($limit = '5')
    {
        $this->_lng  = $this->zfService()->get('MvcTranslator'); // загружаю переводчик
        
        // Использую лямпду как передаваемый объект для выборки
        $resultSet = $this->select(function (Select $select) use ($limit) {
            $select
                ->columns(array(
                    'id',
                ))
                ->join('zf_users_profile', 'zf_users_profile.user_id = '.$this->table.'.id', array(
                    'name',
                    'gender',
                    'photo',
                    'timezone',
                    'birthday'
                ))
                ->join($this->relationsTable['countries'], 'zf_countries.country_id = zf_users_profile.country_id', array(
		    'country_code',
                    'country'       => 'country_'.substr($this->_lng->getLocale(), 0,2)
                ))
                ->join($this->relationsTable['regions'], 'zf_regions.region_id = zf_users_profile.region_id', array(
                    'region'  => 'region_'.substr($this->_lng->getLocale(), 0,2)
                ))         
                ->join($this->relationsTable['cities'], 'zf_cities.city_id = zf_users_profile.city_id', array(
                    'city'  => 'city_'.substr($this->_lng->getLocale(), 0,2)
                ))		    
                ->where('`'.$this->table.'`.`activation` = \'1\'')
                ->order('id DESC')
                ->limit($limit);
                //print  $select->getSqlString($this->adapter->getPlatform()); exit;// SHOW SQL
        });
        return $resultSet;
    }
    
    /**
     * getProfile($id) Выбираю пользователя по ID
     * @param int $id ID в БД
     * @access public
     * @return object Базы данных
     */
    public function getProfile($id)
    {
        $this->_lng  = $this->zfService()->get('MvcTranslator'); // загружаю переводчик
        
        // Использую лямпду как передаваемый объект для выборки
        $resultSet = $this->select(function (Select $select) use ($id) {
            $select
                    ->columns(array(
                    'id',
                    'block',
                    'date_registration',
                    'date_lastvisit',
                    'time_online',
                    'ip',
                    'agent',
                ))
                ->join($this->relationsTable['profile'], $this->relationsTable['profile'].'.user_id = '.$this->table.'.id', array(
                    'name',
                    'gender',
                    'email',
                    'photo',
                    'birthday',
                    'phone',
                    'country_id',                    
                    'region_id',                    
                    'city_id',                    
                    'interests',                    
                    'length',                    
                    'weight',                    
                    'about',                    
                    'alias',                    
                    'personal',
                    'timezone',                    
                ), $select::JOIN_LEFT)
                ->join($this->relationsTable['group'], $this->relationsTable['group'].'.id = '.$this->table.'.role_id', array(
                    'qroup_title' => 'title_'.substr($this->_lng->getLocale(), 0,2),
                ), $select::JOIN_LEFT)
                ->join($this->relationsTable['status'], $this->relationsTable['status'].'.id = '.$this->table.'.status_id', array(
                    'status_title' => 'title',
                ), $select::JOIN_LEFT)
                ->join($this->relationsTable['events'], $this->relationsTable['events'].'.user_id = '.$this->table.'.id', array(
                    'subject'   =>   'subject_'.substr($this->_lng->getLocale(), 0,2),
                    'message'   =>   'message_'.substr($this->_lng->getLocale(), 0,2),
                    'date',
                    'read',
                ), $select::JOIN_LEFT)
                 ->join($this->relationsTable['countries'], 'zf_countries.country_id = '.$this->relationsTable['profile'].'.country_id', array(
                    'country_code'
                ))
                ->where('`'.$this->table.'`.`id` = '.(int)$id)
                ->limit(1);
            //print $select->getSqlString($this->adapter->getPlatform()); // SHOW SQL
        })->current();
        return $resultSet;
    }
}