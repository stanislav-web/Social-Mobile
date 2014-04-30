<?php
namespace Social\Controller; // пространтво имен текущего контроллера

// объявляю зависимости от главных Zend ActionConroller , Zend ViewModel
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Social\Form; // Констуктор форм

/**
 * Контроллер главной страницы
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Controller/IndexController.php
 */
class IndexController extends AbstractActionController
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
     * @var object
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
    
    public function indexAction()
    {     
        $searchForm  = new Form\SimpleSearchForm(); // инициализирую поисковую форму
        $this->_lng  = $this->zfService()->get('MvcTranslator'); // загружаю переводчик
        
        // Устанавливаю заголовок со страницы
        $renderer = $this->zfService()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->_lng->translate('Social Mobile', 'default'));
        
        $indexVars = array(
            // переадаю пользователей
            'users' =>  array(
                'last'      =>  $this->zfService()->get('user.Model')->lastUsers(5)->toArray(),
                'all'       =>  $this->zfService()->get('user.Model')->countUsers(),
                //'female'    =>  $this->zfService()->get('index.Model')->countUsers(2),
                //'male'      =>  $this->zfService()->get('index.Model')->countUsers(1),
            ),
        );
        
        $view = new ViewModel($indexVars);        
        return $view;
    }

    public function searchAction()
    {
        return new ViewModel();
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
