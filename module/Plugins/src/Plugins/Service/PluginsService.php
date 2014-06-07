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
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
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
     * fetchAll($type  =   null, $sort = 'ASC', Select $select = null) метод выборки всех плагинов
     * @param int Тип плагина
     * @param string $sort порядок сортировки
     * @param Select $select description
     * @access public
     * @return object Базы данных
     */
    public function fetchAll($type  =   null, $sort = 'id ASC', Select $select = null)
    {
        if(null === $select) $select = new Select();
        $select->from($this->table);
        if(isset($type) && !empty($type)) $select->where('`type` = '.(int)$type);
        $select->order($sort);
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }
    
    
    /**
     * fetch($id) выборка плагина по ID
     * @param int Тип плагина
     * @access public
     * @return object Базы данных
     */
    public function fetch($id)
    {
        $result =   $this->tableGateway->select(['id' => $id])->current();
        return $result;
    }
    
    /**
     * delete(array $items) удаление плагинов
     * @param  string|array|\Closure $items id плагина
     * @access public
     * @return boolean
     */
    public function delete(array $items)
    {
        $result =   $this->tableGateway->delete($items);
        return $result;
    } 
    
    /**
     * update(array $set, array $items) обновлене плагинов
     * @param array $set массив с установленными знаениями
     * @param  string|array|\Closure $items id плагина
     * @access public
     * @return boolean
     */
    public function update(array $set, array $items)
    {
        $result =   $this->tableGateway->update($set, $items);
        return $result;
    } 
    
    /**
     * add(array $items) добавлене плагина
     * @param  array $items параметры плагина
     * @access public
     * @return boolean
     */
    public function add(array $items)
    {
        $result =   $this->tableGateway->insert($items);
        return $result;
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