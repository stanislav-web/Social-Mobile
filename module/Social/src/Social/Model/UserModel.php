<?php
namespace Social\Model; // инициализирую текущее пространство имен

// подключаю адаптеры Бд
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql; // для запросов
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
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
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
     * Зависимые таблицы
     * @access protected
     * @var string $relationsTable;
     */
    protected $relationsTable = array(
        'profile'   =>  'zf_users_profile',     // профиль
        'group'     =>  'zf_users_group',       // группы
        'status'    =>  'zf_users_status',      // коммерческие статусы
        'events'    =>  'zf_users_events',      // журнал (события)
        'countries' =>  'zf_countries',		// страны
        'regions'   =>  'zf_regions',		// регионы
        'cities'    =>  'zf_cities',		// города
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
		'skype',
		'icq',
		'vk',
		'facebook',                    
                'odnoklassniki',                    
                'country_id',                    
                'region_id',                    
                'city_id',                    
                'interests',                    
                'length',                    
                'weight',                    
                'about',                    
                'alias',                    
                'personal',
                'online',
                'onlinetime',
                'lastvisit',
                'timezone',                    
                ), $select::JOIN_LEFT)
            ->join($this->relationsTable['group'], $this->relationsTable['group'].'.group_id = '.$this->table.'.group_id', array(
                'group_title' => 'title_'.substr($this->_lng->getLocale(), 0,2),
            ), $select::JOIN_LEFT)
            ->join($this->relationsTable['status'], $this->relationsTable['status'].'.status_id = '.$this->table.'.status_id', array(
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
     * isAdmin($id) Проверка админа
     * @param string $login
     * @access public
     * @return object DB `zf_users`
     */
    public function isAdmin($id)
    {
        // Использую лямпду как передаваемый объект для выборки
        $resultSet = $this->select(function (Select $select) use ($id) {
            $select
                    ->columns(array(
                    'id'
                ))
                ->where('`activation` = \'1\' AND `group_id` = \'1\' AND `id` = \''.$id.'\'')
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
                ->where('`'.$this->table.'`.`id` = \''.$id.'\' AND `activation` = \'1\'')
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
        return $resultSet;
    }
    
    /**
     * getCounters($id) Возвращает набор временных данных пользователя
     * @param int $id ID пользователя
     * @access public
     * @return array
     */
    public function getCounters($id)
    {
        $resultSet = $this->select(function (Select $select) use ($id) {
            $select
                    ->columns(array(
                    'id',
                ))
                ->join($this->relationsTable['profile'], $this->relationsTable['profile'].'.user_id = '.$this->table.'.id', array(
                    'onlinetime',
                    'lastvisit' 
                ))
                ->where($this->table.'.`id` = \''.$id.'\'')
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
                    'online',
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
     * getProfile($id) Выбираю пользователя по логину
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
                    'group_id',
                    'status_id',
                    'registerDate',
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
                    'skype',
                    'icq',
                    'vk',
                    'facebook',                    
                    'odnoklassniki',                    
                    'country_id',                    
                    'region_id',                    
                    'city_id',                    
                    'interests',                    
                    'length',                    
                    'weight',                    
                    'about',                    
                    'alias',                    
                    'personal',
                    'online',
                    'onlinetime',
                    'lastvisit',
                    'timezone',                    
                ), $select::JOIN_LEFT)
                ->join($this->relationsTable['group'], $this->relationsTable['group'].'.group_id = '.$this->table.'.group_id', array(
                    'qroup_title' => 'title_'.substr($this->_lng->getLocale(), 0,2),
                ), $select::JOIN_LEFT)
                ->join($this->relationsTable['status'], $this->relationsTable['status'].'.status_id = '.$this->table.'.status_id', array(
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
                ->where('`'.$this->table.'`.`id` = \''.$id.'\' AND `'.$this->table.'`.`activation` = \'1\'')
                ->limit(1);
            //print $select->getSqlString($this->adapter->getPlatform()); // SHOW SQL
        })->current();
        return $resultSet;
    }
}