<?php
namespace Social\Controller; // пространтво имен текущего контроллера

// объявляю зависимости от главных Zend ActionConroller , Zend ViewModel
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Контроллер страниц вывода онлайн
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Controller/OnlineController.php
 */
class OnlineController extends AbstractActionController
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
    
    public function indexAction()
    {     
        $this->_lng  = $this->zfService()->get('MvcTranslator'); // загружаю переводчик

        // Устанавливаю заголовок со страницы
        $renderer = $this->zfService()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->_lng->translate('Who is Online?', 'default'));        
        
        
        $page = $this->params()->fromQuery('page',null);
        
        $onlineVars = array(
            'items'  => $this->zfService()->get('online.Model')->getAll(1),
        );
        
        $view = new ViewModel($onlineVars);        
        return $view;
    }   
    
    public function guysAction()
    {     
        $this->_lng  = $this->zfService()->get('MvcTranslator'); // загружаю переводчик
        
        // Устанавливаю заголовок со страницы
        $renderer = $this->zfService()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->_lng->translate('Guys', 'default'));        
        
        $view = new ViewModel();        
        return $view;
    } 
    
    public function girlsAction()
    {     
        $this->_lng  = $this->zfService()->get('MvcTranslator'); // загружаю переводчик
        
        // Устанавливаю заголовок со страницы
        $renderer = $this->zfService()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->_lng->translate('Girls', 'default'));        
        
        
        $view = new ViewModel();        
        return $view;
    } 
}
