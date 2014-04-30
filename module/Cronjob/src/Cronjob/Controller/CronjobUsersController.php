<?php
namespace Cronjob\Controller; // пространтво имен текущего контроллера

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;

/**
 * Контроллер планировщика  управления пользователями (Консольный вывод)
 * @package Zend Framework 2
 * @subpackage Cronjob
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Cronjob/src/Cronjob/Controller/CronjobUsersController.php
 */
class CronjobUsersController extends AbstractActionController
{
    /**
     * $_log Модель логирования
     * @access private
     * @var object
     */    
    private $_log = null;
    
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
     * onlineAction() Обновление тех кто онлайн
     * @access public
     * @return console
     */    
    public function onlineAction()
    {   
        $request    = $this->getRequest();
        if(!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('Use only for Cronjob console command!');
        }
        
        $type       = $request->getParam('type', 'all');    // параметр для определения действия
        
        // подключаю необходимые модели
        $online     = $this->zfService()->get('online.Model');
        $user       = $this->zfService()->get('user.Model');
        $profile    = $this->zfService()->get('userProfile.Model');        
        
        
        // Достаю модель для логировния планировщика
        if($this->_log == null) $this->_log = $this->zfService()->get('cronjobLog.Model');
        
        $this->_log->write(array(
            'message'   => 'Обновление времени в онлайн',
            'command'   => implode(" ",$request->getContent()),
            'sheduler'  => 'CronjobUsers'
        ));
    }      
}
