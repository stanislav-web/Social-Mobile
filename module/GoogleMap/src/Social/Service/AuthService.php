<?php
namespace Social\Service;

// подключаю интерфейсы ServiceLocator для доступа к POST формы
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
// класс для работы с хранилищем аунтификации
use Zend\Authentication\Storage;

/**
 * AuthService сервис сохранения данных авторизации в Сессии и Cookies
 * $sm->get('auth.Service');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Service/AuthService.php
 */
class AuthService extends Storage\Session implements ServiceLocatorAwareInterface
{

    /**
     * Свойство для хранения Service Locator объекта
     * @access protected
     * @var object $serviceLocator ServiceLocator Instance object
     */
    protected $serviceLocator;

    /**
     * Свойство для хранения POST данных формы
     * @access protected
     * @var object $request
     */
    protected $request;

    /**
     * setServiceLocator(ServiceLocatorInterface $_serviceLocator) Метод реализующий интерфейс установки сервис локатора
     * @access public
     * @var object
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * getServiceLocator(ServiceLocatorInterface $_serviceLocator) Метод реализующий интерфейс загрузки сервис локатора
     * @access public
     * @var object
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * setRememberMe($remember = 0, $time = 1209600) реализация сохранения авторизации в сессии
     * @param int $remember флажок (1,0) для проверки на запоминание данных формы
     * @param int $time timestamp метка для указания времени хранения
     * @access public
     * @return null
     */
    public function setRememberMe($remember = 0, $time = 1209600)
    {
        if($remember==1)
        {
            $this->session->getManager()->rememberMe($time); // установил время хранения
            $this->request = $this->serviceLocator->get('request'); // получил REQUEST как объект
        }
    }

    /**
     * forgetMe() метод уничтожения времени хранения сесси с авторизацией
     * @access public
     * @return null
     */
    public function forgetMe()
    {
        $this->session->getManager()->forgetMe();
    }

}