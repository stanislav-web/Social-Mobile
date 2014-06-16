<?php
namespace Locations\Service;

// подключаю адаптеры Бд
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * RegionsService сервис выдачи регионов и различными операциями со странами
 * $sm->get('regions.Service');
 * @package Zend Framework 2
 * @subpackage Locations
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Locations/src/Locations/Service/RegionsService.php
 */
class RegionsService extends AbstractTableGateway implements ServiceLocatorAwareInterface
{
    /**
     * $__authService Свойство хранения сервиса менеджера
     * @access protected
     * @var type object
     */
    protected $_serviceLocator;

    /**
     * Таблица, к которой обращаюсь
     * @access protected
     * @var string $table;
     */
    protected $table = 'zf_regions';
    
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
     * getDBRegions($country_id = null) метод выборки регионов из БД
     * @param int $country_id код страны, не обязательный параметр
     * @access public
     * @return object Базы данных
     */
    public function getDBRegions($country_id = null)
    {
        $country = (isset($country_id)) ? 'AND `country_id` = '.(int)$country_id : '';
        
        // Использую кэширование (подключаю адаптер)
        $this->cache = $this->getServiceLocator()->get('memcache.Service');
        
        // Проверяю ключ в кэше
        $result = $this->cache->getItem('zf_regions_'.$this->getLocaleCode().'='.$country_id);    
        
        if(!$result)
        {   
            // Использую лямпду как передаваемый объект для выборки
            
            $resultSet = $this->select(function (Select $select) use ($country) {
                $select
                    ->columns(array(
                        'id'        =>  'region_id',
                        'code'      =>  'region_code',
                        'name'      =>  'region_'.$this->getLocaleCode().'',
                    ))
                    ->where('`activation` = \'1\' '.$country.'')
                    ->order('region_'.$this->getLocaleCode().' ASC');
            });
            
            // кэширую выборку
            $resultSet->buffer();
    
            $result = $resultSet->toArray();
            
            $this->cache->setItem('zf_regions_'.$this->getLocaleCode().'='.$country_id, $result);
        }        
        return $result;
    }

    /**
     * getRegionsToSelect($country_id = '') метод составляет список регионов по Стране
     * @access public
     * @return object Базы данных
     */
    public function getRegionsToSelect($country_id = '')
    {
        $rows[0] = array(
            'value' =>   '',
            'label' =>   _('Choose region'),

        );
        foreach($this->getDBRegions($country_id) as $row)
        {
            $rows[] = array (
                'value' => $row['id'],
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