<?php
namespace Admin\Controller; // пространтво имен текущего контроллера

use Admin\Controller\Auth;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Debug\Debug;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
/**
 * Контроллер управления плагинами, Административная панель
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Admin/src/Admin/Controller/PluginController.php
 */
class PluginsController extends Auth\AbstractAuthActionController
{
    /**
     * $__select Выбранные ячейк
     * @access public
     * @var array
     */
    private $__select   =   [];
 
    /**
     * indexAction() Панель управления плагинами
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        // POST обработчик
        $request    =   $this->getRequest();
        if($request->isPost())
        {
            $post   =   $request->getPost();

            if(!isset($post['action']))   $this->flashMessenger()->addMessage($this->lng->translate('Action not selected', 'admin-errors'));
            if(empty($post['select']))    $this->flashMessenger()->addMessage($this->lng->translate('Not selected items', 'admin-errors'));
            else
            {
                // Привожу ключи массива к одному
                $this->__select = $post['select'];
                
                // вызываю экшн
                
                if(!method_exists(__CLASS__, $post['action'])) $this->flashMessenger()->addMessage($this->lng->translate('Action is not available', 'admin-errors'));    
                else return $this->$post['action']();            
            }

            // Возвращаю обратно 
            return $this->redirect()->refresh();
        }
        
        // Устанавливаю МЕТА и заголовок страницы
        
        $this->renderer->headTitle($this->lng->translate('Plugins control', 'admin'));
        
        // Получаю таблицу с содержимым 
        
        $plugins    =   $this->sm->get('plugins.Service'); 
        $fetch      =   $plugins->fetchAll($this->params()->fromQuery('type'));

        // получаю параметры для фильтра
        $filter     =   $this->sm->get('plugintypes.Model')->fetchAll();
        
        // Проверяю и вывожу
        if(count($fetch) < 1) $this->flashMessenger()->addMessage($this->lng->translate('Plugins not found', 'admin-errors'));
        else
        {
	    
	    // Настраиваю постраничный вывод
	    
	    $matches	=   $this->getEvent()->getRouteMatch();
	    $page	=   $matches->getParam('page', 1);
	    $itAdapter	=   new \Zend\Paginator\Adapter\Iterator($fetch);
	    $items	=   new \Zend\Paginator\Paginator($itAdapter);
	    
	    $items->setCurrentPageNumber($page);
	    $items->setItemCountPerPage($this->perpage);
        }
        
        return new ViewModel(
                [
                    'user'	    =>  $this->user->getProfile($this->auth->getIdentity()), // данные об Админе
                    'items'         =>  (isset($items)) ? $items : '',  // вывод строк
                    'filter'        =>  $filter,
                    'messages'      =>  $this->flashMessenger()->getMessages()  // сообщения мессенджера
                ]
        );       
    }
    
    /**
     * viewAction() Просмотр плагина и редактирование
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function viewAction()
    {
        $id     =   $this->params('id');
        echo($id);
    }
    
    /*
     * deleteAction() Удаление 
     * @access public
     */
    public function deleteAction()
    {
        if(empty($this->__select))    $this->flashMessenger()->addMessage($this->lng->translate('Not selected items', 'admin-errors'));
        $result = $this->sm->get('plugins.Service')->delete(['id' => $this->__select]);
        if(!$result)  $this->flashMessenger()->addMessage($this->lng->translate('Error while deleting', 'admin-errors'));
        // Возвращаю обратно 
        return $this->redirect()->refresh();        
    }    
    
     
    /*
     * onAction() Включить 
     * @access public
     */
    public function onAction()
    {
        if(empty($this->__select))    $this->flashMessenger()->addMessage($this->lng->translate('Not selected items', 'admin-errors'));
        $result = $this->sm->get('plugins.Service')->update(['status' => '1'], ['id' => $this->__select]);
        if(!$result)  $this->flashMessenger()->addMessage($this->lng->translate('Error while update status', 'admin-errors'));
        // Возвращаю обратно 
        return $this->redirect()->refresh();        
    }    
    
    /*
     * offAction() Выключить 
     * @access public
     */
    public function offAction()
    {
        if(empty($this->__select))    $this->flashMessenger()->addMessage($this->lng->translate('Not selected items', 'admin-errors'));
        $result = $this->sm->get('plugins.Service')->update(['status' => '0'], ['id' => $this->__select]);
        if(!$result)  $this->flashMessenger()->addMessage($this->lng->translate('Error while update status', 'admin-errors'));
        // Возвращаю обратно 
        return $this->redirect()->refresh();        
    }      
    
}
