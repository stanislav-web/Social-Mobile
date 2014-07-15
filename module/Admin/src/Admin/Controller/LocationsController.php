<?php
namespace Admin\Controller; // пространтво имен текущего контроллера

use Admin\Controller\Auth;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Debug\Debug;
use Google\Client;
/**
 * Контроллер управления локациями, Административная панель
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Admin/src/Admin/Controller/LocationsController.php
 */
class LocationsController extends Auth\AbstractAuthActionController 
{
    /**
     * indexAction() Панель управления пользователями
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        return new ViewModel([]);       
    }
    
    /**
     * compilerAction() Компилятор базы населенных пунктов
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function compilerAction()
    {

        return new ViewModel([]);        
    }
}
