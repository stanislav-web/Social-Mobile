<?php
namespace Social\Service;

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
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Service/CitiesService.php
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
    protected $table = 'zf_cities';

    /**
     * Конструктор адаптера БД
     * @access public
     * @param \Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent
     * @return object DB initialize
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
    public function getDBCities($country_id, $region_id)
    {
        // Использую лямпду как передаваемый объект для выборки
        if($country_id) $ccode = 'AND `country_id` = '.(int)$country_id;
        if($region_id)  $rcode  = 'AND `region_id` = '.(int)$region_id;
        $resultSet = $this->select(function (Select $select) use ($ccode, $rcode) {
            $select
                ->columns(array(
                        'city_id'          =>  'city_id',
                        'name'             =>  'city_'.$this->getLocaleCode().'',
                ))
                ->where('`activation` = \'1\' '.$ccode.' '.$rcode.'')
                ->order('order, city_'.$this->getLocaleCode().' ASC');
        });
        $resultSet = $resultSet->toArray();
        return $resultSet;
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
		echo $select->getSqlString();
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
        if($country_id) $ccode = 'AND `country_id` = '.(int)$country_id;
        if($region_id)  $rcode  = 'AND `region_id` = '.(int)$region_id;
	
        $resultSet = $this->select(function (Select $select) use ($ccode, $rcode) {
            $select
                ->columns(array(
                        'city'    =>  new \Zend\Db\Sql\Expression('DISTINCT LEFT( city_'.$this->getLocaleCode().', 1 )'),
                ))
                ->where('`activation` = \'1\' '.$ccode.' '.$rcode.'')
                ->order('city_'.$this->getLocaleCode().' ASC');
	        //print $select->getSqlString(); exit();// SHOW SQL

        });
	
        $resultSet = $resultSet->toArray();
        return $resultSet;
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