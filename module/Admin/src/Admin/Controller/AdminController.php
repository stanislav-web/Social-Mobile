<?php
namespace Admin\Controller; // пространтво имен текущего контроллера

// объявляю зависимости от главных Zend ActionConroller , Zend ViewModel

use Admin\Controller\Auth;
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
     * $_lng Свойство объекта Zend l18 translator
     * @access protected
     * @var type object
     */
    protected $_lng;
    
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
     * usersAction() Пользователи
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function usersAction()
    {
        $this->_lng = $this->zfService()->get('MvcTranslator'); // загружаю переводчик
        exit($this->authAdmin);
        /**
         * Делаю проверку на авторизацию
         */
        $this->_authAdmin = $this->zfService()->get('authentification.Service');
        $user       = $this->zfService()->get('user.Model');
        $adminFetch  = $user->getProfile($this->_authAdmin->getIdentity());
        if($this->_authAdmin->hasIdentity() && $user->checkRole($adminFetch->id, 4))
        {
            // если уже авторизирован
            // Устанавливаю заголовок со страницы
            $renderer	= $this->zfService()->get('Zend\View\Renderer\PhpRenderer');
            $renderer->headTitle($this->_lng->translate('Users Control', 'admin').' | '.$this->_lng->translate('Social Mobile', 'default'));
	    
	    // Добавляю стиль к нафигации
	    $viewrender = $this->zfService()->get('viewhelpermanager')->get('headLink');
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

        $this->_lng = $this->zfService()->get('MvcTranslator'); // загружаю переводчик

        // если уже авторизирован
        // Устанавливаю заголовок со страницы
        $renderer = $this->zfService()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->_lng->translate('Control Panel', 'admin').' | '.$this->_lng->translate('Social Mobile', 'default'));
            
        $view = new ViewModel(
            [
                'user'	    =>  $this->user->getProfile($this->auth->getIdentity()), // Данные об Админе
		'errorMsg'  =>  $this->flashMessenger()->getErrorMessages()		    
            ]
        );
        return $view;
    }
}
