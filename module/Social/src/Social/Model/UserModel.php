<?php
namespace Social\Model; // инициализирую текущее пространство имен

// подключаю адаптеры Бд
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql; // для запросов
use Zend\Db\Sql\Expression;
use Zend\Paginator\Paginator; // сам пагинатор
use Zend\Paginator\Adapter\DbSelect;// адаптер пагинатора
use Zend\Db\Metadata\Metadata;      // мета данные таблиц

// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
/**
 * Модель пользователя
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
     * $_serviceLocator Свойство для хрения сервис менеджера
     * @access protected
     * @var type object
     */
    protected $_serviceLocator;
    
    /**
     * $_lng Свойство объекта Zend l18 translator
     * @access protected
     * @var object
     */
    protected $_lng;
    
    /**
     * $_page Количтво объектов на странице
     * @access protected
     * @var int
     */    
    protected $_page = 10;
    
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
     * __getMetadata() Получаю названия полей
     * @access private
     * @return array
     */    
    private function __getMetadata()
    {
        $results = [];
        $metadata = new Metadata($this->adapter);
        $table = $metadata->getTable($this->table);
        foreach($table->getColumns() as $column)
        {
            $results[]   =   $column->getName();
        }
        return $results;
    }
    
    /**
     * deleteUser(array $items) удаление записей
     * @param  string|array|\Closure $items id плагина
     * @access public
     * @return boolean
     */
    public function deleteUsers(array $items)
    {
        $result =   $this->delete($items);
        return $result;
    }     
    
    /**
     * updateUsers(array $set, array $items) обновлене элементов
     * @param array $set массив с установленными знаениями
     * @param  string|array|\Closure $items id плагина
     * @access public
     * @return boolean
     */
    public function updateUsers(array $set, array $items)
    {
        $result =   $this->update($set, $items);
        return $result;
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
                $sql = "
                    SELECT 
                    SUM(IF(zf_users_profile.gender =  '1', 1, 0)) as `m`,
                    SUM(IF(zf_users_profile.gender =  '2', 1, 0)) as `f`
                    FROM zf_users_online 
                    INNER JOIN zf_users_profile
                    WHERE zf_users_profile.user_id = zf_users_online.user_id LIMIT 1";

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
                    ->columns([
                    'id'
                ])
                ->where('`id` = '.(int)$user_id.' AND `role_id` = '.(int)$role_id.' AND `state` = \'1\'')
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
                    ->columns([
                    'group_id'
                ])
                ->where('`'.$this->table.'`.`id` = '.(int)$id.' AND `state` = \'1\'')
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
                    ->columns([
                    'id'
                ])
                ->where('`'.$this->table.'`.`login` = \''.$login.'\' AND `state` = \'1\'')
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
                                [
                                    'count' => new \Zend\Db\Sql\Expression('COUNT('.$this->table.'.id)')
                                    ]
                            )
                    ->join('zf_users_profile', 'zf_users_profile.user_id = '.$this->table.'.id', [
                    ])
                    ->where('`state` = \'1\' '.$gender.'')->limit(1);
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
        $updateArray    = [];
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
        $update->where(['id' => $user->id]);
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
        $this->_lng  = $this->getServiceLocator()->get('MvcTranslator'); // загружаю переводчик
        
        // Использую лямпду как передаваемый объект для выборки
        $resultSet = $this->select(function (Select $select) use ($limit) {
            $select
                ->columns([
                    'id',
                ])
                ->join('zf_users_profile', 'zf_users_profile.user_id = '.$this->table.'.id', [
                    'name',
                    'gender',
                    'photo',
                    'timezone',
                    'birthday'
                ])
                ->join('zf_countries', 'zf_countries.country_id = zf_users_profile.country_id', [
		    'country_code',
                    'country'       => 'country_'.substr($this->_lng->getLocale(), 0,2)
                ])
                ->join('zf_regions', 'zf_regions.region_id = zf_users_profile.region_id', [
                    'region'  => 'region_'.substr($this->_lng->getLocale(), 0,2)
                ])         
                ->join('zf_cities', 'zf_cities.city_id = zf_users_profile.city_id', [
                    'city'  => 'city_'.substr($this->_lng->getLocale(), 0,2)
                ])		    
                ->where('`'.$this->table.'`.`state` = \'1\'')
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
        $this->_lng  = $this->getServiceLocator()->get('MvcTranslator'); // загружаю переводчик
        
        // Использую лямпду как передаваемый объект для выборки
        $resultSet = $this->select(function (Select $select) use ($id) {
            $select
                    ->columns([
                    'id',
                    'login',
                    'role_id',
                    'state',
                    'rating',
                    'date_registration',
                    'date_lastvisit',
                    'time_online',
                    'ip',
                    'agent',
                ])
                ->join('zf_users_profile', 'zf_users_profile.user_id = '.$this->table.'.id', [
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
                ], $select::JOIN_LEFT)
                ->join('zf_users_roles', 'zf_users_roles.id = '.$this->table.'.role_id', [
                    'qroup_title' => 'title_'.substr($this->_lng->getLocale(), 0,2),
                ], $select::JOIN_LEFT)
                ->join('zf_users_statuses', 'zf_users_statuses.id = '.$this->table.'.status_id', [
                    'status_title' => 'title',
                ], $select::JOIN_LEFT)
                ->join('zf_users_events', 'zf_users_events.user_id = '.$this->table.'.id', [
                    'subject'   =>   'subject_'.substr($this->_lng->getLocale(), 0,2),
                    'message'   =>   'message_'.substr($this->_lng->getLocale(), 0,2),
                    'date',
                    'read',
                ], $select::JOIN_LEFT)
                 ->join('zf_countries', 'zf_countries.country_id = zf_users_profile.country_id', [
                    'country_code'
                ])
                ->where('`'.$this->table.'`.`id` = '.(int)$id)
                ->limit(1);
            //print $select->getSqlString($this->adapter->getPlatform()); // SHOW SQL
        })->current();
        return $resultSet;
    }
    
    /**
     * getUsers($page, $filter = [], $sort = 'ASC')  Выборка всех записей из таблицы с фильтрами и сортировкой
     * @param int $page Текущая страница
     * @param array $filter Парметры фильтрации выборки (where)
     * @param string [ASC|DESC] параметры сортировки, Ascending, Descendig
     * @return object DB
     */
    public function getUsers($page, $filter = [], $sort = 'ASC') 
    {
        $this->_lng  = $this->getServiceLocator()->get('MvcTranslator'); // загружаю переводчик

        $select = new Select();
        $adapter = $this->adapter;
        $sql = new Sql($this->adapter);
        $select
                ->from($this->table)
                ->join('zf_users_profile', 'zf_users_profile.user_id = '.$this->table.'.id', [
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
                    ]
                )
                ->join('zf_users_roles', 'zf_users_roles.id = '.$this->table.'.role_id', [
                        'role' => 'title_'.substr($this->_lng->getLocale(), 0,2),
                        'role_class' => 'class'   
                    ]
                )
                ->join('zf_users_statuses', 'zf_users_statuses.id = '.$this->table.'.status_id', [
                        'status_title' => 'title',
                    ]
                )
                ->join('zf_countries', 'zf_countries.country_id = zf_users_profile.country_id', [
                        'country_code',
                        'country'       => 'country_'.substr($this->_lng->getLocale(), 0,2),
                    ]
                )
                ->join('zf_regions', 'zf_regions.region_id = zf_users_profile.region_id', [
                        'region'  => 'region_'.substr($this->_lng->getLocale(), 0,2)
                    ]
                )
                ->join('zf_cities', 'zf_cities.city_id = zf_users_profile.city_id', [
                        'city'  => 'city_'.substr($this->_lng->getLocale(), 0,2)
                ]
        );
        
        // Работа с фильтром WHERE
        
        if(!empty($filter))
        {
            // фильтрую. Проверяю сходства полей в базе с запросом GET
            $meta = $this->__getMetadata();
                
            foreach($filter as $k => $v)
            {
                // Фильтрую по текущей таблице
                if($k == 'id' && !empty($v)) 
                {
                    $select->where([$this->table.'.'.$k => (int)trim($v)]);
                    break;
                }
                else
                {
                    if($k == 'name') $select->where(['zf_users_profile.'.$k => trim($v)]);
                    elseif(in_array($k, $meta) && (mb_strlen($v, 'utf-8') > 0)) $select->where([$k => trim($v)]);
                }
            }           
        }
        
        // Работа с сортировкой ORDER BY
        
        if(!empty($filter['order']) && strlen($filter['order']) >= 2) // ID required
        {
            // Проверяю, есть ли в таблице такое поле, если нет то это к профилю
            if(in_array($filter['order'], $meta)) $select->order($this->table.'.'.$filter['order'].' ASC');
            else $select->order('zf_users_profile.'.$filter['order'].' '.$sort);
            // работаю с сортировкой
        }
        else $select->order($this->table.'.id '.$sort);
                
        $selectString = $sql->getSqlStringForSqlObject($select);

        $paginatorAdapter = new DbSelect($select, $adapter);
        $paginator = new Paginator($paginatorAdapter);
        $paginator->setDefaultItemCountPerPage($this->_page);
        $paginator->setPageRange(7);
        $paginator->setCurrentPageNumber($page);

        return $paginator;        
    }     
    
    /**
     * getLoginName($filter = [], $limit = 10)  Выборка login, name для autocomplete
     * @param array $filter Парметры фильтрации выборки (where)
     * @param int $limit лимит выводимых записей
     * @return array
     */
    public function getLoginName($filter = [], $limit = 10) 
    {
        $Adapter = $this->adapter;
        
        $select = new Select();
        $sql = new Sql($Adapter);
        $select
                ->columns(['login'])
                ->from($this->table)
                ->join('zf_users_profile', 'zf_users_profile.user_id = '.$this->table.'.id', ['name']);
        
        // Работа с фильтром WHERE
        
        if(!empty($filter))
        {
            // фильтрую. Проверяю сходства полей в базе с запросом POST
            $meta = $this->__getMetadata();
                
            foreach($filter as $k => $v)
            {
                if((mb_strlen($v, 'utf-8') > 2))
                {
                    if($k == 'name') $select->where->like('zf_users_profile.'.$k, '%'.trim($v).'%');
                    elseif(in_array($k, $meta)) $select->where->like($k, '%'.trim($v).'%');
                    else return [];
                }
                else return [];
            }           
        }
        
        // Работа с сортировкой ORDER BY
        
        foreach($filter as $k => $v)
        {
            if($k == 'name') $select->order('zf_users_profile.name ASC');
            else $select->order($this->table.'.'.$k.' ASC');
            break;    
        }         
        $select->limit($limit);
        $selectString = $sql->getSqlStringForSqlObject($select);
        return $this->adapter->query($selectString, $Adapter::QUERY_MODE_EXECUTE);
    }    
}