<?php
namespace SwebSocialAuth\Controller; // пространтво имен текущего контроллера

// объявляю зависимости от главных Zend ActionConroller , Zend ViewModel
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use SwebSocialAuth;
/**
 * Контроллер модуля SwebSocialAuth
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /vendor/SwebSocialAuth/src/SwebSocialAuth/Controller/SocialController.php
 */
class SocialController extends AbstractActionController
{
    public $config = null;
    
    private function __getConfig()
    {
        return $this->zfService()->get('Config')['swebsocialauth'];
    }
    
    /**
     * zfService() Менеджер зарегистрированных сервисов ZF2
     * @access public
     * @return ServiceManager
     */
    public function zfService()
    {
        return $this->getServiceLocator();
    }   
    
    public function authAction()
    {     
        $adapters = array();
        
        // Собираю всех провайдеров на проверку
        if(null === $this->config) $this->config = $this->__getConfig();
        foreach($this->config as $adapter => $settings) 
        {
            $class = 'SocialAuther\Adapter\\' . ucfirst($adapter);
            $adapters[$adapter] = new $class($settings);
        }        
        
        // Проверяю входящий запрос
        $request    =   $this->params()->fromQuery(); // Запрос GET
        if(!isset($request['provider']) || !array_key_exists($request['provider'], $adapters)) throw new \Exception("Wrong OpenID params");
        
        // Все отлично, прохожу авторизацию по провайдеру
        
        $auther = new \SocialAuther\SocialAuther($adapters[$request['provider']]);
        
        if($auther->authenticate()) 
        {
            // Успешная авторизация
            print_r($auther);
        }
        else
        {
            // Ошибка авторизации
            exit('Failed');
        }
        exit; 
    }
}
