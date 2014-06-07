<?php
namespace Admin\Controller; // пространтво имен текущего контроллера

// объявляю зависимости от главных Zend ActionConroller , Zend ViewModel

use Admin\Controller\Auth; // Базовый контроллер проверки аутентификации
use Zend\View\Model\ViewModel;
use Admin\Form; // Констуктор форм
use Zend\Debug\Debug;

/**
 * Контроллер административной панели
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Admin/src/Admin/Controller/AdminController.php
 */
class AdminController extends Auth\AbstractAuthActionController
{
    
    /**
     * indexAction() Панель управления
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {

        // если уже авторизирован
        // Устанавливаю заголовок со страницы
        $this->renderer->headTitle($this->lng->translate('Control Panel', 'admin'));
            
        $view = new ViewModel(
            [
                'user'	    =>  $this->user->getProfile($this->auth->getIdentity()), // Данные об Админе
            ]
        );
        return $view;
    }
}
