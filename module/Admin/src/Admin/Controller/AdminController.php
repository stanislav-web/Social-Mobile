<?php
namespace Admin\Controller; // пространтво имен текущего контроллера

// объявляю зависимости от главных Zend ActionConroller , Zend ViewModel

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Form; // Констуктор форм
use Zend\Debug\Debug;

/**
 * Контроллер административной панели
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Admin/src/Admin/Controller/AdminController.php
 */
class AdminController extends AbstractActionController
{

    /**
     * $_lng Свойство объекта Zend l18 translator
     * @access protected
     * @var type object
     */
    protected $_lng;

    /**
     * $_authAdmin Авторизированый
     * @access protected
     * @var type object
     */
    protected $_authAdmin = null;
    
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

        /**
         * Делаю проверку на авторизацию
         */
        $this->_authAdmin = $this->zfService()->get('authentification.Service');
        $user       = $this->zfService()->get('user.Model');
        $adminFetch  = $user->getProfile($this->_authAdmin->getIdentity());
        if($this->_authAdmin->hasIdentity() && $user->isAdmin($adminFetch->id))
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

        /**
         * Делаю проверку на авторизацию
         */
        $this->_authAdmin = $this->zfService()->get('authentification.Service');
        $user       = $this->zfService()->get('user.Model');
        $userFetch  = $user->getProfile($this->_authAdmin->getIdentity());
        if($this->_authAdmin->hasIdentity() && $user->isAdmin($userFetch->id))
        {
            // если уже авторизирован
            // Устанавливаю заголовок со страницы
            $renderer = $this->zfService()->get('Zend\View\Renderer\PhpRenderer');
            $renderer->headTitle($this->_lng->translate('Control Panel', 'admin').' | '.$this->_lng->translate('Social Mobile', 'default'));
       
            $view = new ViewModel(
                array(
                    'user'	    =>  $userFetch,
		    'errorMsg'	    =>  $this->flashMessenger()->getErrorMessages()		    
                )
            );
            return $view;
        }
        else return $this->logoutAction();
    }

    /**
     * authAction() Окно авторизации
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function authAction()
    {
        $this->_lng = $this->zfService()->get('MvcTranslator'); // загружаю переводчик
        $request    = $this->getRequest(); // Запрос через форму

        $authForm = new Form\AuthForm($this->_lng); // Форма авторизации

        //Делаю проверку на авторизацию

        $this->_authAdmin = $this->zfService()->get('authentification.Service');
        // если уже авторизирован, выносим его отсюда
        if($this->_authAdmin->hasIdentity()) return $this->redirect()->toRoute('admin/admin/index');

        /**
         * Проверяю, когда форма была отправлена
         */
        if($request->isPost())
        {
            $authValidator = $this->zfService()->get('adminAuth.Validator'); // валидатор формы авторизации
            $authForm->setInputFilter($authValidator->getInputFilter()); // устанавливаю фильтры на форму авторизации
            $authForm->setData($request->getPost());
            if($authForm->isValid())
            {
                // теперь проверяю по базе пользователей
                // вытягиваю сервис авторизации
                $admin  =   $this->zfService()->get('sign.Model');
                $auth   =   $admin->signAuth($request->getPost('login'), $request->getPost('password'), $remember = 1);
                $id     =   $this->_authAdmin->getIdentity();

                if($auth && $admin->isAdmin($id)) return $this->redirect()->toRoute('admin'); // успешная авторизация
                else
                {
                    // ошибка при авторизации
                    $this->flashMessenger()->addErrorMessage("Authentication failed! Wrong Login or Password");
                }
            }
        }
        else
        {
            /**
            * Делаю проверку на авторизацию
            */
            $id     =   $this->_authAdmin->getIdentity();
            if($this->_authAdmin->hasIdentity()&& $admin->isAdmin($id))
            {
                // если уже авторизирован, выносим его отсюда
                $this->flashMessenger()->addErrorMessage("You are already authorized. If need to do sign with another account please re-authorize");
            }            
        }
            
        // Устанавливаю заголовок со страницы
        $renderer = $this->zfService()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->_lng->translate('Control Panel', 'admin').' | '.$this->_lng->translate('Social Mobile', 'default'));
            
        $view = new ViewModel(
                array(
                    'authForm'      =>  $authForm, // форма авторизации
                    'errorMsgAuth'  =>  $this->flashMessenger()->getErrorMessages(), // сообщения об ошибках
                )
        );
        return $view;
    }
    
    /**
     * logoutAction() Выход из Панели Управления
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function postAction()
    {
	// Проверяю POST это или нет
	$request    = $this->getRequest(); // Запрос через форму
	
        if($request->isPost()) //  пошла POST форма
        {
	    $arrPost = $request->getPost()->toArray();	
	    print_r($arrPost);
	    exit('sas');	    
	}
	else
	{
	    // Создаю сообщение об ошибке
	    $this->flashMessenger()->addErrorMessage('Action error! You can not send form without POST!');
	}
	return $this->redirect()->toRoute('admin');
    }
    
    /**
     * logoutAction() Выход из Панели Управления
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function logoutAction() {
        $this->zfService()->get('sign.Model')->getSessionStorage()->forgetMe(); // очищаю запоминание
        $this->zfService()->get('sign.Model')->getAuthService()->clearIdentity(); // очищаю весь слой авторизции    
        return $this->redirect()->toUrl('/admin/auth');
    }
}
