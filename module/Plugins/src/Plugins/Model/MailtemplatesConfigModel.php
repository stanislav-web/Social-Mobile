<?php
namespace Plugins\Model; // инициализирую текущее пространство имен

// подключаю адаптеры Бд
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Модель управения почтовыми шабонами
 * $sm->get('mailtemplates.Model');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Plugins/src/Plugins/Model/MailtemplatesConfigModel.php
 */
class MailtemplatesConfigModel implements ServiceLocatorAwareInterface
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
    protected $table    = 'zf_config_mailtemplates';

    /**
     * Шлюз БД
     * @access protected
     * @var object $tableGateway;
     */
    protected $tableGateway;

    /**
     * Конструктор адаптера БД
     * @access public
     * @param \Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent
     * @return object DB initialize
     */
    public function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }

    /**
     * get($system) Метод выборки всех настроек из zf_config_mailtemplates
     * @param string $system Системный код
     * @access public
     * @param \Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent
     * @return object DB initialize
     */
    public function get($system)
    {
        $service = $this->getServiceLocator()->get('plugins.Service');
        foreach($service->getActivePlugins() as $value)
        {
            if($this->table == $value['system'])
            {
                $resultSet = $this->tableGateway->select(function (Select $select) use($system) {
                    $select
                        ->columns(array(
                        'template',
                        'subject'       =>  'subject_'.$this->getLocaleCode().'',
                        'message'       =>  'message_'.$this->getLocaleCode().'',
                        'sms'           =>  'sms_'.$this->getLocaleCode().'',
                        'system'
                    ))
                    ->where('`system` = \''.$system.'\'');
                    
                })->current();

                //$select->getSqlString($this->tableGateway->getPlatform()); // Показую запрос
                break;
            }
        }
        return $resultSet;
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