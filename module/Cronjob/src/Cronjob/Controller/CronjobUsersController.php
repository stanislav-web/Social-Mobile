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
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
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
        //$online     = $this->getServiceLocator()->get('online.Model');
        //$user       = $this->getServiceLocator()->get('user.Model');
        //$profile    = $this->getServiceLocator()->get('userProfile.Model');        
        
        
        // Достаю модель для логировния планировщика
        $this->_log = $this->getServiceLocator()->get('cronjobLog.Model');
        
        $this->_log->write(array(
            'message'   => 'Обновление времени в онлайн',
            'command'   => implode(" ",$request->getContent()),
            'sheduler'  => 'CronjobUsers'
        ));
    }      
}
