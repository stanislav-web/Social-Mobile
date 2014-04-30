<?php
namespace Social\Service;

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
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Service/RegionsService.php
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
    protected $table = 'zf_regions';

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
     * getDBRegions($country_code = null) метод выборки регионов из БД
     * @param string $country_code код страны, не обязательный параметр
     * @access public
     * @return object Базы данных
     */
    public function getDBRegions($country_code = null)
    {
        // Использую лямпду как передаваемый объект для выборки
        if($country_code) $country_code = 'AND `country_code` = \''.$country_code.'\'';
        $resultSet = $this->select(function (Select $select) use ($country_code) {
            $select
                ->columns(array(
                        'id'        =>  'region_id',
                        'code'      =>  'region_code',
                        'name'      =>  'region_'.$this->getLocaleCode().'',
                ))
                ->where('`activation` = \'1\' '.$country_code.'')
                ->order('region_'.$this->getLocaleCode().' ASC');
        });
        $resultSet = $resultSet->toArray();
        return $resultSet;
    }
    
    /**
     * getRegionsID($country_code, $region_code) Достать ID региона
     * @param string $country_code код страны
     * @param string $region_code код регыиона стран
     * @access public
     * @return object Базы данных
     */
    public function getRegionID($country_code, $region_code)
    {
        // Использую лямпду как передаваемый объект для выборки
        $resultSet = $this->select(function (Select $select) use ($country_code, $region_code) {
            $select
                ->columns(array(
                        'id'        =>  'region_id',
                ))
                ->where('`country_code` = \''.$country_code.'\' AND `region_code` = \''.$region_code.'\'');
        })->current();
        if(!$resultSet)  throw new \Exception('`'.$this->table.'` No records found');
        return $resultSet;
    }    

    /**
     * getRegionsToSelect() метод составляет список регионов в форму
     * @access public
     * @return object Базы данных
     */
    public function getRegionsToSelect($country_code = '')
    {
        $rows[0] = array(
            'value' =>   '',
            'label' =>   _('Choose region'),

        );
        foreach($this->getDBRegions($country_code) as $row)
        {
            $rows[$row['id']] = array (
                'value' => $row['code'],
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