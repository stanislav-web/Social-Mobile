<?php
namespace Social\Controller; // пространтво имен текущего контроллера

// объявляю зависимости от главных Zend ActionConroller , Zend ViewModel
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Social\Controller\Auth; // Базовый контроллер проверки аутентификации
use Social\Form; // Констуктор форм

/**
 * Контроллер управления пользователями
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Controller/UserController.php
 */
class UserController extends Auth\AbstractAuthActionController
{
    
    public function journalAction()
    {
        // Проверка авторизации
        $this->_authUser = $this->getServiceLocator()->get('authentification.Service');
        if($this->_authUser->hasIdentity() == false) return $this->redirect()->toUrl('/'); // главную сразу    
                
        // аторизированный
            
        $this->_sessionUser->auth = $this->getServiceLocator()->get('user.Model')->getUserProfileByLogin($this->_authUser->getIdentity()); // Записываю в сессию все о пользователе
        $this->_uid = $this->_sessionUser->auth->id; // UID Пользователя
        $this->_lng  = $this->getServiceLocator()->get('MvcTranslator');    // загружаю переводчик
        $events = $this->getServiceLocator()->get('userEvents.Model');      // загружаю модель с событиями
        $menu   = $this->getServiceLocator()->get('menuItems.Service');     // загружаю модель текущего меню
        
        $item = $menu->getItemByRoute('journal');

        // Устанавливаю заголовок со страницы
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($item->title);
        
        $eventsVars = array(
                'journal'   =>  $this->getServiceLocator()->get('userEvents.Model')->getEvents($this->_uid, 1, 100),
                'title'     =>  $item->title,                   // Заголовок в меню
                'user'      =>  $this->_sessionUser->auth,      // ВСЕ об авторизованом пользователе
            );

        $viewModel = new ViewModel($eventsVars);
        return $viewModel;            
    }
    
    /**
     * usersAction() Все пользователи
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function usersAction()
    {
        $viewModel = new ViewModel();
        return $viewModel;         
    }    
    
    /**
     * indexAction() Просмотр пользователя
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        
        $slug = $this->params()->fromRoute('slug');     // определяю какого пользователя запросили
        if(!$slug) return $this->redirect()->toRoute('users'); // если нет, перекидываю на users
        exit($slug);
        

            $viewModel = new ViewModel($userVars);
            return $viewModel;            
    }

    /**
     * profileAction() Личный кабинет пользователя
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function profileAction()
    {
        $events         = $this->getServiceLocator()->get('userEvents.Model');    // загружаю модель с событиями
            
        // Устанавливаю заголовок со страницы
        $renderer = $this->sm->get('Zend\View\Renderer\PhpRenderer');
	$userFetch =  $this->user->getProfile($this->auth->getIdentity());
        
        $renderer->headTitle($userFetch->name);
            
	return new ViewModel([
                'notices' =>  [ // новые уведомления
                    'journal'   =>  null, //$this->getServiceLocator()->get('userEvents.Model')->getEvents($userProfile->id, 1, 100),
                    'mail'      =>  null, // $this->getServiceLocator()->get('userMail.Model')->getMails($this->_uid, 1, 100),
                    'wall'      =>  null, // $this->getServiceLocator()->get('userWall.Model')->getWalls($this->_uid, 1, 100),
                ],
                'user'          =>  $userFetch,			// ВСЕ об авторизованом пользователе
                'personalForm'  =>  new Form\PersonalForm(),    // форма смены персонального статуса	    
	]);
    }    
    
    /**
     * personalAction() AJAX обрабочик для обновления статуса
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function personalAction()
    {
        $view = new ViewModel();
        $request    = $this->getRequest();     // Запрос
        if($request->isXmlHttpRequest() && $request->isPost()) //  пошла POST форма
        {
	    $post   = $request->getPost();
	    
            // Обновляю статус
	    
	    $result = $this->user->updateUsers(['personal'  =>	$post['personal']],
		['id'	=>  $this->auth->getIdentity()]);
	    
            if($result) 
            {
                // Публикую на стену, если был запрос

                if($post['share'])
                { 
                    $wall = $this->sm->get('flashWall.Model');
                    $wall->set($post['personal'], $this->auth->getIdentity());
                }
            }
        }
        return $this->redirect()->toRoute('profile'); // ставлю редирект
    }    
    
    
    
    /**
     * logoutAction() Выход из Панели Управления
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function logoutAction() {
        if(null === $this->_authUser) $this->_authUser = $this->getServiceLocator()->get('authentification.Service');
        
        $this->getServiceLocator()->get('sign.Model')->getSessionStorage()->forgetMe(); // очищаю запоминание        
        $this->_authUser->clearIdentity(); // очищаю весь слой авторизции   
        return $this->redirect()->toUrl('/');
    }         
}
