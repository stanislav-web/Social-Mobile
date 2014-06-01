<?php
namespace DBTranslations\Model; // инициализирую текущее пространство имен

// подключаю адаптеры Бд
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql; // для запросов

// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
/**
 * Модель использует сущность имеющихся в базе языков
 * $sm->get('language.Model');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/DBTranslations/Model/LanguageModel.php
 */
class LanguageModel extends  AbstractTableGateway implements ServiceLocatorAwareInterface
{
    /**
     * Таблица, к которой обращаюсь
     * @access protected
     * @var string $table;
     */
    protected $table = 'zf_languages';
    
    /**
     * $_serviceLocator Свойство для хрения сервис менеджера
     * @access protected
     * @var type object
     */
    protected $_serviceLocator;

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
     * getLangages() Достать все языки
     * @param int $page - страница текущая
     * @param int $perpage - записей на страницу
     * @param string $order - сортировка
     * @param array $filter - записей на страницумассив с объектами фильтрации
     * @return void
     */
    public function getLangages()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from($this->table);
 
        $statement = $sql->prepareStatementForSqlObject($select);
        
        $results = $statement->execute();
        if($results->count() > 0) return $results;
        else return null;
    }    
    
    
}