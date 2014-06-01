<?php
namespace Plugins\Service;

// подключаю адаптеры Бд
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

/**
 * PluginsService сервис хранилища плагинов
 * $sm->get('plugins.Service');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Plugins/src/Plugins/Service/PluginsService.php
 */
class PluginsService
{

    /**
     * Таблица, к которой обращаюсь
     * @access protected
     * @var string $table;
     */
    protected $table = 'zf_plugins';

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
     * getPlugins(Select $select = null) метод выборки всех плагинов
     * @param Select $select description
     * @access public
     * @return object Базы данных
     */
    public function getPlugins(Select $select = null)
    {
        if(null === $select) $select = new Select();
        $select->from($this->table);
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }
   
    /**
     * getActivePlugins() метод выборки всех активных плагинов
     * @param boolean $flag true
     * @access public
     * @return object Базы данных
     */
    public function getActivePlugins()
    {
        $this->resultSet = $this->tableGateway->select(function (Select $select) {
            $select
                ->columns(array(
                        'system'
                    )
                )->where('`status` = \'1\'');
        });
        return $this->resultSet->toArray();
    }
}