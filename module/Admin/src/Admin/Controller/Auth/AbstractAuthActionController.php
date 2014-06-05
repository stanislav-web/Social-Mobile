<?php
namespace Admin\Controller\Auth; // пространтво имен текущего контроллера

use Zend\Mvc\Controller\AbstractActionController;
use Zend\EventManager\EventManagerInterface;

/**
 * AbstractAuthActionController Контроллер глобальной проверки авторизации админа
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Admin/src/Admin/Controller/Abstract/AbstractAuthActionController.php
 */
abstract class AbstractAuthActionController extends AbstractActionController 
{
    /**
     * Белый список маршрутов, которые не попадают под проверку авторизации
     * @access protected
     * @var array 
     */
    protected $allowedRoutes = ['sign'];
    
    /**
     * Объект сохраняющий состояние авторизации админа
     * @access protected
     * @var object 
     */
    protected $auth = null;    
    
    /**
     * Объект сохраняющий профиль авторизировавшегося
     * @access protected
     * @var object ModelUser
     */
    protected $user = null;  
    
    /**
     * $lng Свойство объекта Zend l18 translator
     * @access protected
     * @var type object
     */
    protected $lng;    
    
    /**
     * $renderer Свойство объекта Zend\View\Renderer\PhpRenderer
     * @access protected
     * @var type object
     */
    protected $renderer;     
    
    /**
     * $perpage Объектов, выводимых на страницу
     * @access protected
     * @var int 
     */
    protected $perpage    =   10; 
    
    /**
     * $sm Сервис локатор
     * @access protected
     * @var Zend\ServiceManager 
     */
    protected $sm    =   null; 
    
    /**
     * $messenger Объект мессенджера
     * @access protected
     * @var Zend\Mvc\Controller\Plugin\FlashMessenger 
     */    
    protected $messenger    =   null;
    
    /**
     * setEventManager(EventManagerInterface $events) Событие на отработку контроллера
     * @param \Zend\EventManager\EventManagerInterface $events
     * @access public
     * @return null
     */
    public function setEventManager(EventManagerInterface $events) 
    {
        parent::setEventManager($events);
        
        // Получаю необходимые сервисы авторизации, локалей, мессенджера...
        
        $this->sm           =   $this->getServiceLocator();
        $this->auth         =   $this->sm->get('authentification.Service');         // сервис аутентификации
        $this->user         =   $this->sm->get('user.Model');                       // модель получения данных о пользователе
        $this->lng          =   $this->sm->get('MvcTranslator');                    // загружаю переводчик
        $this->renderer     =   $this->sm->get('Zend\View\Renderer\PhpRenderer');   // управление МЕТА и заголовками таблицы
        $this->messenger    =   $this->flashMessenger();                            // Флэш мессенджер

        $events->attach(\Zend\Mvc\MvcEvent::EVENT_DISPATCH, function($e) 
        {
            $routeName = $e->getRouteMatch()->getMatchedRouteName();
            
            // Проверяю допустимый роут, и если его нет в списке то проверяю на Админа
            if(!in_array($routeName,$this->allowedRoutes))
            {
                // Проверяю авторизацию и роль Администратора
                if(!$this->auth->hasIdentity() && !$this->user->checkRole($this->auth->getIdentity(), 4)) // 4 - роль Админа (у меня)
                {
                    // Перекидываю на страницу авторизации
                    return $this->redirect()->toRoute('sign');
                }     
            }
        }, 100);
    }
}