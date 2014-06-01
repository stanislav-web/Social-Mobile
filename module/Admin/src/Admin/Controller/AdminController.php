<?php
namespace Admin\Controller; // пространтво имен текущего контроллера

// объявляю зависимости от главных Zend ActionConroller , Zend ViewModel

use Admin\Controller\Auth; // Базовый контроллер проверки аутентификации
use Zend\View\Model\ViewModel;
use Admin\Form; // Констуктор форм
use Zend\Debug\Debug;

/**
 * Контроллер административной панели
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Admin/src/Admin/Controller/AdminController.php
 */
class AdminController extends Auth\AbstractAuthActionController
{
    

    /**
     * pluginsAction() Управление плагинами
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function pluginsAction()
    {
        // Устанавливаю МЕТА и заголовок страницы
        
        $this->renderer->headTitle($this->lng->translate('Plugins control', 'admin'));
        
        // Получаю таблицу с содержимым 
        
        $plugins = $this->getServiceLocator()->get('plugins.Service'); 
        $fetch  =   $plugins->getPlugins();
        
        // Проверяю и вывожу
        if(count($fetch) < 1) $this->flashMessenger()->addMessage($this->lng->translate('Plugins not found', 'admin-errors'));
        else
        {
	    
	    // Настраиваю постраничный вывод
	    
	    $matches	=   $this->getEvent()->getRouteMatch();
	    $page	=   $matches->getParam('page', 1);
	    $itAdapter	=   new \Zend\Paginator\Adapter\Iterator($fetch);
	    $paginator	=   new \Zend\Paginator\Paginator($itAdapter);
	    
	    $paginator->setCurrentPageNumber($page);
	    $paginator->setItemCountPerPage($this->items);
	                
        }
        
        return new ViewModel(
                [
                    'user'	    =>  $this->user->getProfile($this->auth->getIdentity()), // данные об Админе
                    'items'         =>  $paginator,  // вывод всех
                    'messages'      =>  $this->flashMessenger()->getMessages()  // сообщения мессенджера
                ]
        );
    }    
    
    /**
     * usersAction() Пользователи
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function usersAction()
    {
        $this->lng = $this->getServiceLocator()->get('MvcTranslator'); // загружаю переводчик
        /**
         * Делаю проверку на авторизацию
         */
        $adminFetch  = $this->user->getProfile($this->auth->getIdentity());
        if($this->_authAdmin->hasIdentity() && $user->checkRole($adminFetch->id, 4))
        {
            // если уже авторизирован
            // Устанавливаю заголовок со страницы
            $this->renderer->headTitle($this->lng->translate('Users Control', 'admin'));
	    
	    // Добавляю стиль к нафигации
	    $viewrender = $this->getServiceLocator()->get('viewhelpermanager')->get('headLink');
	    $viewrender->appendStylesheet('/css/mobile/paginator.css');
	    
	    /**
	     * Настраиваю постраничный вывод
	     */
	    $matches	=   $this->getEvent()->getRouteMatch();
	    $page	=   $matches->getParam('page', 1);
	    $itAdapter	=   new \Zend\Paginator\Adapter\Iterator($user->getUsers());
	    $paginator	=   new \Zend\Paginator\Paginator($itAdapter);
	    
	    $paginator->setCurrentPageNumber($page);
	    $paginator->setItemCountPerPage(3);
	    
	    
	    //$usersFetch	=   ;
	
            $view = new ViewModel(
                array(
                    'user'         =>  $adminFetch, // админ
                    'items'        =>  $paginator,  // все пользователи
                )
            );
            return $view;
        }
        else return $this->logoutAction();
    }    
    
    /**
     * indexAction() Панель управления
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {

        $this->lng = $this->getServiceLocator()->get('MvcTranslator'); // загружаю переводчик

        // если уже авторизирован
        // Устанавливаю заголовок со страницы
        $this->renderer->headTitle($this->lng->translate('Control Panel', 'admin'));
            
        $view = new ViewModel(
            [
                'user'	    =>  $this->user->getProfile($this->auth->getIdentity()), // Данные об Админе
		'errorMsg'  =>  $this->flashMessenger()->getErrorMessages()		    
            ]
        );
        return $view;
    }
}
