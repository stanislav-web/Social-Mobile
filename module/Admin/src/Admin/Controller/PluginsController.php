<?php
namespace Admin\Controller; // пространтво имен текущего контроллера

use Admin\Controller\Auth;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Form; // Констуктор форм
use Zend\Debug\Debug;

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
    
    /**
     * indexAction() Панель управления
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        die('Plugins will be able soon...');
    }
}
