<?php
namespace SwebSocialAuth\View\Helper;

use Zend\View\Helper\AbstractHelper;
// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Помошник вида SocialAuth выводит ссылки на авторизацию в социальных сетях
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /vendor/SwebSocialAuth/src/SwebSocialAuth/View/Helper/SocialAuth.php
 */
class SocialAuth extends AbstractHelper  implements ServiceLocatorAwareInterface  {
    
    /**
     * Чтобы получить доступ к Сервис Менеджеру, необходимо использовать его интерфейс и переопределить
     * его get() и set() методы
     */
    protected $_sm;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->_sm = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->_sm;
    }
    
    public function __invoke() 
    {
        $view = $this->getView();
        $config = $this->_sm->getServiceLocator()->get('Config')['swebsocialauth'];
        
        foreach($config as $adapter => $settings) 
        {
            $class = 'SocialAuther\Adapter\\' . ucfirst($adapter);
            $adapters[$adapter] = new $class($settings);
        }        
        return $view->render('sweb-social-auth/social/auth', array('adapters' => $adapters));        
    }
}