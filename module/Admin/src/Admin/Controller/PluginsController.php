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
            if(!isset($post['action']))   $this->messenger->addErrorMessage($this->lng->translate('Action not selected', 'admin-messages'));
            if(empty($post['select']))    $this->messenger->addErrorMessage($this->lng->translate('Not selected items', 'admin-messages'));
            else
            {
                // Привожу ключи массива к одному // вызываю экшн
                $this->__select = $post['select'];
                
                if(!method_exists(__CLASS__, $post['action'])) $this->messenger->addErrorMessage($this->lng->translate('Action is not available', 'admin-messages'));    
                else return $this->$post['action']();            
            }

            // Возвращаю обратно 
            return $this->redirect()->refresh();
        }
        
        // Устанавливаю МЕТА и заголовок страницы
        $title  =   $this->lng->translate('Plugins control', 'admin');
        $this->renderer->headTitle($title);
        
        // Получаю таблицу с содержимым 
        
        $plugins    =   $this->sm->get('plugins.Service'); 
        $fetch      =   $plugins->fetchAll($this->params()->fromQuery('type'), 'id DESC');

        // получаю параметры для фильтра
        $filter     =   $this->sm->get('plugintypes.Model')->fetchAll();
        
        // Проверяю и вывожу
        if(count($fetch) < 1) $this->messenger->addErrorMessage($this->lng->translate('Plugins not found', 'admin-messages'));
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
                    'title'         =>  $title
                ]
        );       
    }
    
    /**
     * editAction() Просмотр плагина и редактирование
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        
        // POST обработчик
        $request    =   $this->getRequest();
        $id         =   (int)$this->params('id');
        $plugins    =   $this->sm->get('plugins.Service');
        
        if($request->isPost())
        {
            if($request->getPost()['readonly'] == 1)    $this->messenger->addErrorMessage($this->lng->translate('This plugin is only for reading, the changes may lead to malfunction of the system', 'admin-messages'));
            else
            {
                // Update values
                $plugins->update((array)$request->getPost(), ['id' => $id]);
                $this->messenger->addSuccessMessage($this->lng->translate('Plugin configuration\'s has been changed', 'admin-messages'));
            }

            // Возвращаю обратно 
            return $this->redirect()->toUrl('/admin/plugins/edit/'.$id);
        }        
        else $this->messenger->addInfoMessage($this->lng->translate('All fields must be required', 'admin-messages'));

        // Получаю параметры плагина по id

        $fetch  =   $plugins->fetch($id);
        
        if($fetch) 
        {
            // Устанавливаю МЕТА и заголовок страницы
            $title  =   sprintf($this->lng->translate('Plugins control ▶ %s' , 'admin'), $fetch->title);
            $this->renderer->headTitle($title);
            
            if($fetch->readonly) $this->messenger->addErrorMessage($this->lng->translate('This plugin is only for reading, the changes may lead to malfunction of the system', 'admin-messages'));

            // получаю параметры для фильтра
            $filter     =   $this->sm->get('plugintypes.Model')->fetchAll();            
            
            return new ViewModel(
                [
                    'user'	    =>  $this->user->getProfile($this->auth->getIdentity()), // данные об Админе
                    'filter'        =>  $filter,
                    'item'          =>  (isset($fetch)) ? $fetch : '',  // вывод строк
                    'title'         =>  $title
                ]
            );            
        }
        else
        {
            $this->messenger->addErrorMessage($this->lng->translate('Plugin not found', 'admin-messages'));
            return $this->redirect()->toRoute('plugins');
        }
    }
    
    /*
     * addAction() Добавление 
     * @access public
     */
    public function addAction()
    {
        
        // POST обработчик
        $request    =   $this->getRequest();
        
        if($request->isPost())
        {
            // Add values

            $result = $this->sm->get('plugins.Service')->add((array)$request->getPost());
            if($result) $this->messenger->addSuccessMessage($this->lng->translate('Plugin has been registered', 'admin-messages'));
            else  $this->messenger->addErrorMessage($this->lng->translate('An error occurred while registering plugin', 'admin-messages'));
            // Возвращаю обратно 
            return $this->redirect()->toRoute('plugins');
        }        
        else $this->messenger->addInfoMessage($this->lng->translate('All fields must be required', 'admin-messages'));
        // Устанавливаю МЕТА и заголовок страницы
        $title  =   $this->lng->translate('Plugins control ▶ Register plugin' , 'admin');
        $this->renderer->headTitle($title);

        // получаю параметры для фильтра
        $filter     =   $this->sm->get('plugintypes.Model')->fetchAll();            
            
        return new ViewModel(
            [
                'user'	    =>  $this->user->getProfile($this->auth->getIdentity()), // данные об Админе
                'filter'        =>  $filter,
                'title'         =>  $title
            ]
        );            
    }
    
    /*
     * deleteAction() Удаление 
     * @access public
     */
    public function deleteAction()
    {
        if(empty($this->__select))    $this->messenger->addErrorMessage($this->lng->translate('Not selected items', 'admin-messages'));
        $result = $this->sm->get('plugins.Service')->delete(['id' => $this->__select]);

        if(empty($result))  $this->messenger->addErrorMessage($this->lng->translate('Error while deleting', 'admin-messages'));
        else  $this->messenger->addSuccessMessage($this->lng->translate('Plugin was deleted successfuly', 'admin-messages'));
        // Возвращаю обратно 
        return $this->redirect()->refresh();        
    }    
     
    /*
     * onAction() Включить 
     * @access public
     */
    public function onAction()
    {
        if(empty($this->__select))    $this->messenger->addErrorMessage($this->lng->translate('Not selected items', 'admin-messages'));
        $result = $this->sm->get('plugins.Service')->update(['status' => '1'], ['id' => $this->__select]);
        if(!$result)  $this->messenger->addErrorMessage($this->lng->translate('Error while update status', 'admin-messages'));
        // Возвращаю обратно 
        return $this->redirect()->refresh();        
    }    
    
    /*
     * offAction() Выключить 
     * @access public
     */
    public function offAction()
    {
        if(empty($this->__select))    $this->messenger->addErrorMessage($this->lng->translate('Not selected items', 'admin-messages'));
        $result = $this->sm->get('plugins.Service')->update(['status' => '0'], ['id' => $this->__select]);
        if(!$result)  $this->messenger->addErrorMessage($this->lng->translate('Error while update status', 'admin-messages'));
        // Возвращаю обратно 
        return $this->redirect()->refresh();        
    }      
    
}
