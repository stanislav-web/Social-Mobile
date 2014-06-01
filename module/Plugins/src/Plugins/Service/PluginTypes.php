<?php
namespace Plugins\Service;

// подключаю адаптеры Бд
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

/**
 * PluginTypes сервис типов плагинов
 * $sm->get('plugintypes.Service');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Plugins/src/Plugins/Service/PluginTypes.php
 */
class PluginTypes
{

    /**
     * Таблица, к которой обращаюсь
     * @access protected
     * @var string $table;
     */
    protected $table = 'zf_plugin_types';

    /**
     * Шлюз БД
     * @access protected
     * @var object $tableGateway;
     */
    protected $tableGateway;

    /**
     * Результат выборки
     * @access protected
     * @var array $resultSet;
     */
    protected $resultSet;

    /**
     * Конструктор адаптера БД
     * @access public
     * @param \Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent
     * @return object DB initialize
     */
    public function __construct($dbAdapter)
    {
        $this->tableGateway = new TableGateway($this->table, $dbAdapter);
    }
    
    /**
     * fetch(Select $select = null) метод выборки всех типов
     * @param Select $select description
     * @access public
     * @return object Базы данных
     */
    public function fetch(Select $select = null)
    {
        if(null === $select) $select = new Select();
        $select->from($this->table);
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }
}