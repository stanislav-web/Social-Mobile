<?php
namespace Locations\Service;

// подключаю адаптеры Бд
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * CitiesService сервис выдачи городов
 * $sm->get('cities.Service');
 * @package Zend Framework 2
 * @subpackage Locations
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Locations/src/Locations/Service/CitiesService.php
 */
class CitiesService extends AbstractTableGateway implements ServiceLocatorAwareInterface
{
    /**
     * $_serviceLocator Свойство хранения сервиса менеджера
     * @access protected
     * @var type object
     */
    protected $_serviceLocator;

    /**
     * Таблица, к которой обращаюсь
     * @access protected
     * @var string $table;
     */
    protected $table = 'zf_cities';

    /**
     * Объект кэширования
     * @access protected
     * @var object $cache;
     */
    protected $cache = null;
    
    /**
     * setServiceLocator(ServiceLocatorInterface $_serviceLocator) Интерфейс установки сервис локатора
     * @access public
     * @var object
     */
    public function setServiceLocator(ServiceLocatorInterface $_serviceLocator)
    {
        $this->_serviceLocator = $_serviceLocator;
    }

    /**
     * getServiceLocator(ServiceLocatorInterface $_serviceLocator) Интерфейс загрузки сервис локатора
     * @access public
     * @var object
     */
    public function getServiceLocator()
    {
        return $this->_serviceLocator;
    }

    /**
     * Конструктор адаптера БД + объект кэширования
     * @access public
     * @param object Zend\Db\Adapter\Adapter $dbAdapter
     * @return mixed
     */
    public function __construct($dbAdapter)
    {
        $this->adapter = $dbAdapter;
        $this->initialize();
    }

    /**
     * getDBCities($country_code = null, $region_id = null) метод выборки городов из БД
     * @param int $country_id код страны, не обязательный параметр
     * @param int $region_id  код региона, не обязательный параметр
     * @access public
     * @return object Базы данных
     */
    public function getDBCities($country_id = null, $region_id = null)
    {
        // Использую лямпду как передаваемый объект для выборки
        $country = (isset($country_id)) ? 'AND `country_id` = '.(int)$country_id : '';
        $region = (isset($region_id)) ? 'AND `region_id` = '.(int)$region_id : '';
        
        // Использую кэширование (подключаю адаптер)
        $this->cache = $this->getServiceLocator()->get('memcache.Service');

        // Проверяю ключ в кэше
        $result = $this->cache->getItem('zf_cities_'.$country_id.'-'.$region_id);

        if(!$result)
        {   
            // Делаю выборку и кэширую результат запроса
            $resultSet = $this->select(function (Select $select) use ($country, $region) {
                $select
                    ->columns(array(
                        'city_id'          =>  'city_id',
                        'name'             =>  'city_'.$this->getLocaleCode().'',
                    ))
                    ->where('`activation` = \'1\' '.$country.' '.$region.'')
                    ->order('order, city_'.$this->getLocaleCode().' ASC');
                });
            // кэширую выборку
            $resultSet->buffer();
    
            $result = $resultSet->toArray();
            $this->cache->setItem('zf_cities_'.$country_id.'-'.$region_id, $result);
        }
        return $result;
    }
    
    /**
     * getDBCitiesByShort($country_code = null, $region_code = null) метод выборки городов из БД
     * @param string $country_code код страны, не обязательный параметр
     * @param string $region_code  код региона, не обязательный параметр
     * @access public
     * @return object Базы данных
     */
    public function getDBCitiesByShort($country_code, $region_code, $city)
    {
        // Использую лямпду как передаваемый объект для выборки
        if($country_code) $ccode = 'AND `country_code` = \''.$country_code.'\'';
        if($region_code)  $rcode  = 'AND `region_code` = \''.$region_code.'\'';
        $resultSet = $this->select(function (Select $select) use ($city) {
            $select
                ->columns(array(
                        'city_id',
                        'name'          =>  'city_'.$this->getLocaleCode().'',
                ))
		->where("`activation` = '1' {$ccode} {$rcode} AND `city_{$this->getLocaleCode()}` LIKE '{$city}%'")
                ->order('city_'.$this->getLocaleCode().' ASC');
        });
        $resultSet = $resultSet->toArray();
        return $resultSet;
    }    

    /**
     * getFirstLetter($country_id = null, $region_id = null) Выборка первых букв в алфавитном порядке
     * по коду страны и региону
     * @param int $country_id код страны, не обязательный параметр
     * @param int $region_id  код региона, не обязательный параметр
     * @access public
     * @return object Базы данных
     */
    public function getFirstLetter($country_id, $region_id)
    {
        if($country_id) $country = 'AND `country_id` = '.(int)$country_id;
        if($region_id)  $region  = 'AND `region_id` = '.(int)$region_id;
        
        // Использую кэширование (подключаю адаптер)
        
        $this->cache = $this->getServiceLocator()->get('memcache.Service');

        // Проверяю ключ в кэше
        $result = $this->cache->getItem('zf_cities-'.$this->getLocaleCode().'-'.$country_id.'-'.$region_id);

        if(!$result)
        {   
            // Делаю выборку и кэширую результат запроса
            $resultSet = $this->select(function (Select $select) use ($country, $region) {
            $select
                ->columns(array(
                        'city'    =>  new \Zend\Db\Sql\Expression('DISTINCT LEFT( city_'.$this->getLocaleCode().', 1 )'),
                ))
                ->where('`activation` = \'1\' '.$country.' '.$region.'')
                ->order('city_'.$this->getLocaleCode().' ASC');
                //print $select->getSqlString(); exit();// SHOW SQL

            });

            // кэширую выборку
            $resultSet->buffer();
    
            $result = $resultSet->toArray();
            $this->cache->setItem('zf_cities-'.$this->getLocaleCode().'-'.$country_id.'-'.$region_id, $result);
        }
        return $result;
    }    
    
    /**
     * getCitiesToSelect($country_id = null, $region_id = null) метод составляет список городов по стране и региону в форму
     * @param int $country_id код страны
     * @param int $region_id код региона стран
     * @access public
     * @return object Базы данных
     */
    public function getCitiesToSelect($country_id = null, $region_id = null)
    {
        $rows[0] = array(
            'value' =>   '0',
            'label' =>   _('Choose city'),

        );
        foreach($this->getDBCities($country_id, $region_id) as $row)
        {
            $rows[] = array (
                'value' => $row['city_id'],
                'label' => $row['name'],
            );
        }
        return $rows;
    }

    /**
     * getLocaleCode() код текущей локализации
     * @access private
     * @return string
     */
    public function getLocaleCode()
    {
        $locale = $this->getServiceLocator()->get('MvcTranslator');
        $locale = substr($locale->getLocale(), 0,2);
        
        return $locale;
    } 
}