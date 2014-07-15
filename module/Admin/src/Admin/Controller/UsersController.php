<?php
namespace Admin\Controller; // пространтво имен текущего контроллера

use Admin\Controller\Auth;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Debug\Debug;
use Google\Client;
/**
 * Контроллер управления пользователями, Административная панель
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Admin/src/Admin/Controller/UsersController.php
 */
class UsersController extends Auth\AbstractAuthActionController 
{
    /**
     * $__select Выбранные ячейки
     * @access public
     * @var array
     */
    private $__select   =   [];
 
    /**
     * indexAction() Панель управления пользователями
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
            
            if(isset($post['roleAction']) && !empty($post['roleAction']))
            {
                // работаю с ролями
                if(empty($post['select']))    $this->messenger->addErrorMessage($this->lng->translate('Not selected items', 'admin-messages'));
                else
                {
                    $this->__select = $post['select'];
                    return $this->roleAction();            
                }             
            }
            else
            {
                if(!isset($post['action']))   $this->messenger->addErrorMessage($this->lng->translate('Action not selected', 'admin-messages'));
                if(empty($post['select']))    $this->messenger->addErrorMessage($this->lng->translate('Not selected items', 'admin-messages'));
                else
                {
                    // Привожу ключи массива к одному // вызываю экшн
                    $this->__select = $post['select'];
                
                    if(!method_exists(__CLASS__, $post['action'])) $this->messenger->addErrorMessage($this->lng->translate('Action is not available', 'admin-messages'));    
                    else return $this->$post['action']();            
                }                
            }

            // Возвращаю обратно 
            return $this->redirect()->refresh();
        }
        
        // Устанавливаю МЕТА и заголовок страницы
        $title  =   $this->lng->translate('Users control', 'admin');
        $this->renderer->headTitle($title);
        
        // Получаю модель
        $users      =   $this->sm->get('user.Model'); 

        // Получаю данные таблицы пользователей
        $items      =   $users->getUsers($this->params()->fromRoute('page', 1), $this->params()->fromQuery());

        // использую стандартный метод подсчета
        if($items->getCurrentItemCount() < 1) $this->messenger->addErrorMessage($this->lng->translate('Users not found', 'admin-messages'));

        return new ViewModel(
                [
                    'items'         =>  $items,  // вывод строк
                    'roles'         =>  $this->sm->get('roles.Model')->getRoles(),
                ]
        );       
    }
    
    /**
     * viewAction() Просмотр
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function viewAction()
    {
        // получаю id записи из параметров URI
        $id         =   (int)$this->params('id');
        $item       =   $this->sm->get('user.Model')->getProfile($id);
        
        if($item)
        {
            // Устанавливаю МЕТА и заголовок страницы
            $title  =   sprintf($this->lng->translate('Users control ▶ %s' , 'admin'), $item->name);
            $this->renderer->headTitle($title);
        }
        else 
        {
            // пользователя не существует
            $this->messenger->addErrorMessage($this->lng->translate('User not found', 'admin-messages'));
            return $this->redirect()->toRoute('users');
        }
        return new ViewModel(
                [
                    'title'     =>  $item->name, // заголовок         
                    'item'      =>  $item,  // вывод строк
                ]
        );        
    }
    
    /**
     * editAction() Редактирование
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        // POST обработчик
        $request    =   $this->getRequest();
        if($request->isPost())
        {
            $post   =   $request->getPost();

            Debug::dump($post);
            exit;
            // Возвращаю обратно 
            return $this->redirect()->refresh();
        }         
        
        // получаю id записи
        $id         =   (int)$this->params('id');
        $item       =   $this->sm->get('user.Model')->getProfile($id);
        
        if($item)
        {
            // Устанавливаю МЕТА и заголовок страницы
            $title  =   sprintf($this->lng->translate('Users control ▶ %s' , 'admin'), $item->name);
            $this->renderer->headTitle($title);
        }
        else 
        {
            $this->messenger->addErrorMessage($this->lng->translate('User not found', 'admin-messages'));
            return $this->redirect()->toRoute('users');
        }
        return new ViewModel(
                [
                    'title'         =>  $item->name, // заголовок         
                    'item'          =>  $item,  // вывод строк
                    'roles'         =>  $this->sm->get('roles.Model')->getRoles(),
                    'countries'     =>  $this->sm->get('countries.Service')->getDBCountries(),
                    'regions'       =>  $this->sm->get('regions.Service')->getDBRegions($item->country_id),
                    'cities'        =>  $this->sm->get('cities.Service')->getDBCities($item->country_id, $item->region_id),
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
        $result = $this->sm->get('user.Model')->deleteUsers(['id' => $this->__select]);

        if(empty($result))  $this->messenger->addErrorMessage($this->lng->translate('Error while deleting', 'admin-messages'));
        else  $this->messenger->addSuccessMessage($this->lng->translate('Item(s) was deleted successfuly', 'admin-messages'));
        // Возвращаю обратно 
        return $this->redirect()->refresh();        
    }    
     
    /*
     * enableAction() Активировать 
     * @access public
     */
    public function enableAction()
    {
        if(empty($this->__select))    $this->messenger->addErrorMessage($this->lng->translate('Not selected items', 'admin-messages'));
        $result = $this->sm->get('user.Model')->updateUsers(['state' => '1'], ['id' => $this->__select]);
        if(!$result)  $this->messenger->addErrorMessage($this->lng->translate('Error while update status', 'admin-messages'));
        else  $this->messenger->addSuccessMessage($this->lng->translate('Item(s) was update successfuly', 'admin-messages'));
        
        // Возвращаю обратно 
        return $this->redirect()->refresh();        
    }    
    
    /*
     * disableAction() Деактивировать 
     * @access public
     */
    public function disableAction()
    {
        if(empty($this->__select))    $this->messenger->addErrorMessage($this->lng->translate('Not selected items', 'admin-messages'));
        $result = $this->sm->get('user.Model')->updateUsers(['state' => '0'], ['id' => $this->__select]);
        if(!$result)  $this->messenger->addErrorMessage($this->lng->translate('Error while update status', 'admin-messages'));
        else  $this->messenger->addSuccessMessage($this->lng->translate('Item(s) was update successfuly', 'admin-messages'));
        
        // Возвращаю обратно 
        return $this->redirect()->refresh();        
    }      
    
    /*
     * banAction() Забанен
     * @access public
     */
    public function banAction()
    {
        if(empty($this->__select))    $this->messenger->addErrorMessage($this->lng->translate('Not selected items', 'admin-messages'));
        $result = $this->sm->get('user.Model')->updateUsers(['state' => '3'], ['id' => $this->__select]);
        if(!$result)  $this->messenger->addErrorMessage($this->lng->translate('Error while update status', 'admin-messages'));
        else  $this->messenger->addSuccessMessage($this->lng->translate('Item(s) was update successfuly', 'admin-messages'));
        
        // Возвращаю обратно 
        return $this->redirect()->refresh();        
    }
    
    /*
     * roleAction() Установить роль
     * @access public
     */
    public function roleAction()
    {
        // при изменении ролей, проверяю админа
        if($this->user->checkRole($this->auth->getIdentity(), 4))
        {

            $request    =   $this->getRequest();
            $role_id    =   (int)$request->getPost()['roleAction'];
        
            if(empty($this->__select))    $this->messenger->addErrorMessage($this->lng->translate('Not selected items', 'admin-messages'));
            $result = $this->sm->get('user.Model')->updateUsers(['role_id' => $role_id], ['id' => $this->__select]);
            if(!$result)  $this->messenger->addErrorMessage($this->lng->translate('Error while update the role', 'admin-messages'));   
            else  $this->messenger->addSuccessMessage($this->lng->translate('Item(s) was update successfuly', 'admin-messages'));
        }
        else $this->messenger->addErrorMessage($this->lng->translate('Only an administrator can assign roles to users', 'admin-messages'));
        // Возвращаю обратно 
        return $this->redirect()->refresh();        
    }  

    /*
     * jsonAction() Ajax action
     * @access public
     * @return json
     */
    public function jsonAction()
    {
        $request    = $this->getRequest();
        $response   =   [];

        if($request->isXmlHttpRequest())
        {   
            // Получаю модель
            $users      =   $this->sm->get('user.Model'); 

            // Получаю данные таблицы пользователей
            $post = (array)$request->getPost();
            
            if(!empty($post)) $response   =   $users->getLoginName($post);
        }     
        return new JsonModel($response);
    }    
}
