<?php
namespace Social\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Помошник вида routeHelper выводит информацию о текущем роутинге в шаблон
 * например так $this->getInfo()['version'] PHP 5.4
 * или $this->getInfo()->version если invoke преобразует (object)
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/View/Helper/RouteHelper.php
 */
class RouteHelper extends AbstractHelper {

    /**
     * Объект для Сервис Менеджера
     * @access protected
     * @var object $sm ServiceManager Instance object
     */
    protected $sm;
    
    /**
     * Объект для Сервис Менеджера
     * @access protected
     * @var object $sm ServiceManager Instance object
     */
    protected $request;
    
    /**
     * Свойство для хранения текущего сегмента url
     * @access public
     * @var array $route
     */
    public $route = array();

    public function __construct($app) {
        $this->sm = $app->getServiceManager();
        return $this->route();
    }
    
    /**
     * route() метод достает текущий маршрут для URL
     * @access public
     * @return string
     */
    public function route()
    {
        $router = $this->sm->get('router');
        $request = $this->sm->get('request');
        $routeMatch = $router->match($request);
        if (!is_null($routeMatch))
        {
            $this->route = array(
                                    'controller'    => $routeMatch->getMatchedRouteName(),
                                    'action'	    => $routeMatch->getParam('action'),
                                    'request'	    => $request->getServer()->get('QUERY_STRING'),
                                );

        }
        else return null;
    }

    /**
     * post() метод получает $_POST параметры
     * @param mixed $param Ключ/Ключи масссива POST
     * @access public
     * @return string
     */
    public function post($param = null)
    {
        $request = $this->sm->get('request');
        if(!isset($param)) return $request->getPost();
        else
        {
            if(!is_array($param)) return $request->getPost($param);
            else return $request->getPost($param)->toArray();
        }
    }
    
    /**
     * get() метод получает $_GET параметры
     * @param mixed $param Ключ/Ключи масссива GET
     * @access public
     * @return string
     */
    public function get($param = null)
    {
        $request = $this->sm->get('request');
        if(!isset($param)) return $request->getQuery();
        else
        {
            if(!is_array($param)) return $request->getQuery($param);
            else return $request->getQuery($param)->toArray();
        }
    }    
}