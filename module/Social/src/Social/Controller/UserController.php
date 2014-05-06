<?php
namespace Social\Controller; // пространтво имен текущего контроллера

// объявляю зависимости от главных Zend ActionConroller , Zend ViewModel
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
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
class UserController extends AbstractActionController
{
    
    /**
     * $_lng Свойство объекта Zend l18 translator
     * @access protected
     * @var type object
     */
    protected $_lng;
    
    /**
     * $_authUser Авторизированый
     * @access protected
     * @var type object
     */
    protected $_authUser = null;
    
    /**
     * zfService() Менеджер зарегистрированных сервисов ZF2
     * @access public
     * @return ServiceManager
     */
    public function zfService()
    {
        return $this->getServiceLocator();
    }   
    
    
    public function journalAction()
    {
        // Проверка авторизации
        $this->_authUser = $this->zfService()->get('authentification.Service');
        if($this->_authUser->hasIdentity() == false) return $this->redirect()->toUrl('/'); // главную сразу    
                
        // аторизированный
            
        $this->_sessionUser->auth = $this->zfService()->get('user.Model')->getUserProfileByLogin($this->_authUser->getIdentity()); // Записываю в сессию все о пользователе
        $this->_uid = $this->_sessionUser->auth->id; // UID Пользователя
        $this->_lng  = $this->zfService()->get('MvcTranslator');    // загружаю переводчик
        $events = $this->zfService()->get('userEvents.Model');      // загружаю модель с событиями
        $menu   = $this->zfService()->get('menuItems.Service');     // загружаю модель текущего меню
        
        $item = $menu->getItemByAlias('journal');

        // Устанавливаю заголовок со страницы
        $renderer = $this->zfService()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($item->title);
        
        $eventsVars = array(
                'journal'   =>  $this->zfService()->get('userEvents.Model')->getEvents($this->_uid, 1, 100),
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
        $this->_authUser = $this->zfService()->get('authentification.Service');
        
 
        if($this->_authUser->hasIdentity() == false) return $this->redirect()->toUrl('/'); // главную сразу
        else
        {   
	    $user           = $this->zfService()->get('user.Model')->getProfile($this->_authUser->getIdentity()); // Записываю пользователя
            $this->_lng     = $this->zfService()->get('MvcTranslator'); // загружаю переводчик
            $events         = $this->zfService()->get('userEvents.Model');    // загружаю модель с событиями
            
            // Устанавливаю заголовок со страницы
            $renderer = $this->zfService()->get('Zend\View\Renderer\PhpRenderer');
            $renderer->headTitle($user->name);
            
            $userVars = array(
            
                // переадаю пользователей
                'notices' =>  array( // новые уведомления
                    'journal'   =>  $this->zfService()->get('userEvents.Model')->getEvents($user->id, 1, 100),
                    'mail'      =>  null, // $this->zfService()->get('userMail.Model')->getMails($this->_uid, 1, 100),
                    'wall'      =>  null, // $this->zfService()->get('userWall.Model')->getWalls($this->_uid, 1, 100),
                ),
                'user'          =>  $user,      // ВСЕ об авторизованом пользователе
                'personalForm'  =>  new Form\PersonalForm(),        // форма смены персонального статуса
            );

            $viewModel = new ViewModel($userVars);
            return $viewModel;            
        } 
    }    
    
    /**
     * personalAction() AJAX обрабочик для обновления статуса
     * 
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function personalAction()
    {
        $view = new ViewModel();
        $request    = $this->getRequest();     // Запрос
        if($request->isPost()) //  пошла POST форма
        {
            $personal = $request->getPost('personal');

            // Обновляю статус
            if($this->_authUser == null) $this->_authUser = $this->zfService()->get('authentification.Service');
            $userId = $this->_authUser->getIdentity(); // ID пользователя
            
            $service = $this->zfService()->get('userProfile.Model');
            $result = $service->updatePersonal($userId, $personal);
            if($result == '1') 
            {
                // Публикую на стену, если был запрос

                $share = $request->getPost('share');
                if($share == '1')
                { 
                    $wall = $this->zfService()->get('flashWall.Model');
                    $wall->set($personal, $userId);
                }
                $this->redirect()->toRoute('profile'); // ставлю редирект
            }
            $this->redirect()->toRoute('users'); // ставлю редирект
            return false;
        }
        return false;
    }    
    
    
    
    /**
     * logoutAction() Выход из Панели Управления
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function logoutAction() {
        if(null === $this->_authUser) $this->_authUser = $this->zfService()->get('authentification.Service');
        
        $this->zfService()->get('sign.Model')->getSessionStorage()->forgetMe(); // очищаю запоминание        
        $this->_authUser->clearIdentity(); // очищаю весь слой авторизции   
        return $this->redirect()->toUrl('/');
    }         
}
