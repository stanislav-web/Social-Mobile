<?php
namespace Admin\Controller; // пространтво имен текущего контроллера

use Admin\Controller\Auth;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Debug\Debug;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\EventManager\EventManagerInterface;

/**
 * Контроллер управления крупномасштабными рассылками, Административная панель
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Admin/src/Admin/Controller/DistributionsController.php
 */
class DistributionsController extends Auth\AbstractAuthActionController
{
    /**
     * $configList  настройки провайдеров
     * @acceess public
     * @var array
     */
    public $configList  =   null;
    
    /**
     * $provider  Объект провайдера
     * @acceess public
     * @var object
     * @see \Submissions\Factory\ProviderFactory
     */
    public $provider  =   null;    
    
    /**
     * setEventManager(EventManagerInterface $events) Событие на отработку контроллера
     * @param \Zend\EventManager\EventManagerInterface $events
     * @access public
     * @return null
     */
    public function setEventManager(EventManagerInterface $events) 
    {
        parent::setEventManager($events);
        
        $events->attach(\Zend\Mvc\MvcEvent::EVENT_DISPATCH, function($e) 
        {
            $this->configList   =   $this->__getProviderList();
        }, 99); // в очеред 99, потому что перед этим наследовался - 100й
    }
    
    /**
     * indexAction() Панель управления рассылками
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        // Устанавливаю МЕТА и заголовок страницы
        $title  =   $this->lng->translate('Submissions control', 'admin');
        $this->renderer->headTitle($title);        
        
        return new ViewModel(
            [
                'providers' =>  $this->configList
            ]
        );      
    }
    
    /**
     * viewAction() Дктальный просмотр провайдера
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function viewAction()
    {
        $id   =   $this->params('provider');
        
        if($this->configList[$id])
        {
            // Устанавливаю МЕТА и заголовок страницы
        
            $title  =   sprintf($this->lng->translate('Submissions control ▶ %s' , 'admin'), $id);          
            $this->renderer->headTitle($title);    
        
            // получаю методы для работы с провайдером
            $factory        = $this->getServiceLocator()->get('Submissions\Factory\ProviderFactory');
            $this->provider = $factory->getProvider($id);
            //Debug::dump($this->provider->send(['msisdn' => 380954916517, 'message' => 'Test']));
            
            
            //exit;
            // Делаю запрос
            //$response = $this->provider->subscribe(['email' => 'test3@mail.ua'], 3690382);
            //$response = $this->provider->unsubscribe('test@mail.ua', 3690382);
            //$response = $this->provider->exclude('test@mail.ua', 3690382);
            //$response = $this->provider->exportContacts();
            //$response = $this->provider->getListContacts(3690382);
            //$response = $this->provider->sendMessage(18741758);
            //$response = $this->provider->getMailingStatus(18741758);
            //$response = $this->provider->createEmailMessage('Sender Name', 'imbizdevelop@gmail.com', 'Subject', '<b>Message...</b>', 3690382, '<b>Message...</b>');
            //$response = $this->provider->deleteMessage(18741758);
            //$response = $this->provider->getMailingInfo(3690382);
            //$response = $this->provider->activateContacts(3690382);
            
            //$response = $this->provider->forceSendMessage('Sender Name', 'imbizdevelop@gmail.com', 'Subject', '<b>Message...</b>', 3690382, '<b>Message...</b>');


            
            if(!isset($response['error']))
            {
                //Debug::dump($response);
            }
            else $this->messenger->addErrorMessage($response['error']);
            
            return new ViewModel(
                [
                    'title'     =>  $id,
                    'provider'  =>  $this->configList[$id]
                ]
            );             
        }
        else
        {
            // Провайдер не найден
            $this->messenger->addErrorMessage($this->lng->translate('Distribution provider not found', 'admin-messages'));
            return $this->redirect()->toRoute('distributions');
        }
    }
    
    /**
     * __getProviderList() Загрузка настроек провайдеров
     * @access private
     * @return array
     */
    private function __getProviderList()
    {
        // Загружаю настройки провайдеров
        return $this->sm->get('Config')["Submissions\Provider\Config"];
    }
}
