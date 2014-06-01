<?php
namespace Social\Controller\Auth; // пространтво имен текущего контроллера

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
 * @filesource /module/Social/src/Social/Controller/Abstract/AbstractAuthActionController.php
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
     * setEventManager(EventManagerInterface $events) Событие на отработку контроллера
     * @param \Zend\EventManager\EventManagerInterface $events
     * @access public
     * @return null
     */
    public function setEventManager(EventManagerInterface $events) 
    {
        parent::setEventManager($events);
        
        // Получаю сервис авторизации и модель пользователей
        $this->auth     = $this->getServiceLocator()->get('authentification.Service');
        $this->user     = $this->getServiceLocator()->get('user.Model'); // Модель получения данных о пользователе

        $events->attach(\Zend\Mvc\MvcEvent::EVENT_DISPATCH, function($e) 
        {
            $routeName = $e->getRouteMatch()->getMatchedRouteName();
            
            // Проверяю допустимый роут, и если его нет в списке то проверяю на Админа
            if(!in_array($routeName,$this->allowedRoutes))
            {
                // Проверяю авторизацию 
                if(!$this->auth->hasIdentity()) 
                {
                    // Перекидываю на страницу авторизации
                    return $this->redirect()->toRoute('sign');
                }     
            }
            
        }, 100);
    }
}