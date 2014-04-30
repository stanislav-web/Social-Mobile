<?php
namespace Social\Service;

// подключаю адаптеры Бд
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * CountriesService сервис выдачи стран и различными операциями со странами
 * $sm->get('сountries.Service');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Service/CountriesService.php
 */
class CountriesService extends AbstractTableGateway implements ServiceLocatorAwareInterface
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
    protected $table = 'zf_countries';

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
     * getDBCountries() метод выборки всех стран из БД
     * @access public
     * @return object Базы данных
     */
    public function getDBCountries()
    {
        // Использую лямпду как передаваемый объект для выборки
        $resultSet = $this->select(function (Select $select) {
            $select
                ->columns(array(
                        'id'        =>  'country_id',
                        'code'      =>  'country_code',
                        'name'      =>  'country_'.$this->getLocaleCode().'',
                    )
                )
                ->where('`activation` = \'1\'')
                ->order('country_'.$this->getLocaleCode().' ASC');
        });
        $resultSet = $resultSet->toArray();
        return $resultSet;
    }

    /**
     * getCountryID($country_code) Достать ID страны
     * @param string $country_code код страны
     * @access public
     * @return object Базы данных
     */
    public function getCountryID($country_code)
    {
        // Использую лямпду как передаваемый объект для выборки
        $resultSet = $this->select(function (Select $select) use ($country_code) {
            $select
                ->columns(array(
                        'id'        =>  'country_id',
                ))
                ->where('`country_code` = \''.$country_code.'\'');
        })->current();
        if(!$resultSet)  throw new \Exception('`'.$this->table.'` No records found');
        return $resultSet;
    }       
    
    /**
     * getCountriesToSelect() метод составление формы из списка стран
     * @access public
     * @return object Базы данных
     */
    public function getCountriesToSelect()
    {
        $rows[0] = array(
            'value' =>   '0',
            'label' =>   _('Choose country'),

        );
        foreach($this->getDBCountries() as $row)
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