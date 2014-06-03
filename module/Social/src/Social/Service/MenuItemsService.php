<?php
namespace Social\Service; // инициализирую текущее пространство имен

// подключаю адаптеры Бд
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Сервис для выборки элементов меню для всего сайта
 * использовать сервис менеджер в модели
 * $sm->get('MenuItems.Model');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Service/MenuItemsService.php
 */
class MenuItemsService implements ServiceLocatorAwareInterface
{
    /**
     * Шлюз БД
     * @access protected
     * @var object $tableGateway;
     */
    protected $tableGateway;
    
    /**
     * $_serviceLocator Свойство для хрения сервис менеджера
     * @access protected
     * @var type object
     */
    protected $_serviceLocator;

    /**
     * Основная таблица, к которой обращаюсь
     * @access protected
     * @var string $table;
     */
    protected $table = 'zf_menu_items';

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
     * fetchAll() Чтение всей таблицы с пунктами меню
     * @access public
     * @return object Menu Items
     */
    public function fetchAll()
    {    
        // Использую лямпду как передаваемый объект для выборки

        $resultSet = $this->tableGateway->select(function (Select $select) {
            $select
                ->columns([
                    'title'         =>  'title_'.$this->getLocaleCode().'',
                    'description'   =>  'description_'.$this->getLocaleCode().'',
                    'icon',
                    'alias',
                ])
                ->order('order ASC');
        });
        if($resultSet) return $resultSet;        
        
        return $this->tableGateway->select()->toArray();
    }
    
    /**
     * getMenuItems($menu) Пункты главного меню Панели управления
     * @param $menu Код элементов меню, который проходит в шаблон
     * @access public
     * @return object Menu Items
     */
    public function getMenuItems($menu)
    {
        // Использую лямпду как передаваемый объект для выборки

        $resultSet = $this->tableGateway->select(function (Select $select) use($menu) {
            $select
                ->columns([
                    'title' =>  'title_'.$this->getLocaleCode().'',
                    'description'   =>  'description_'.$this->getLocaleCode().'',
                    'icon',
                    'alias',
                ])
                ->where('`activation` = \'1\' AND `menu` = \''.$menu.'\' AND `children` IS NULL')
                ->order('order ASC');
        });
        if($resultSet) return $resultSet;
    }
    
    /**
     * getItemByAlias($menu) Достать пункт меню по алиасу
     * @param алиас элемента
     * @access public
     * @return object Menu Item
     */
    public function getItemByAlias($alias)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use($alias) {
            $select
                ->columns([
                    'title' =>  'title_'.$this->getLocaleCode().'',
                    'description'   =>  'description_'.$this->getLocaleCode().'',
                    'icon',
                    'alias',
                ])
                ->where('`activation` = \'1\' AND `alias` = \''.$alias.'\'')
                ->order('order ASC')
                ->limit(1);
        });
        if($resultSet) return $resultSet; // PHP5.4 in use
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