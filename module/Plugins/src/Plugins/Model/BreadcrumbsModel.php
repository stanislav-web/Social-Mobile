<?php
namespace Plugins\Model; // инициализирую текущее пространство имен

// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Debug\Debug;
use Zend\View\Helper\AbstractHelper;
use Zend\Navigation\Service\DefaultNavigationFactory;

/**
 * Модель вывода шаблона с хлебными крошками
 * $sm->get('Breadcrumbs.Model');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Plugins/src/Plugins/Model/BreadcrumbsModel.php
 */
class BreadcrumbsModel extends DefaultNavigationFactory implements ServiceLocatorAwareInterface
{
    /**
     * $_serviceLocator Свойство для хрения сервис менеджера
     * @access protected
     * @var type object
     */
    protected $_serviceLocator;

    /**
     * Системный код плагина
     * @access protected
     * @var string $table;
     */
    protected $table    = 'breadcrumbs';
    
    /**
     * setServiceLocator(ServiceLocatorInterface $_serviceLocator) Метод реализующий интерфейс установки сервис локатора
     * @access public
     * @var object
     */
    public function setServiceLocator(ServiceLocatorInterface $_serviceLocator)
    {
        $this->_serviceLocator = $_serviceLocator;
    }

    /**
     * getServiceLocator(ServiceLocatorInterface $_serviceLocator) Метод реализующий интерфейс загрузки сервис локатора
     * @access public
     * @var object
     */
    public function getServiceLocator()
    {
        return $this->_serviceLocator;
    }
    /**
     * config() Параметры хлебных крошек
     * @return array
     */
    public function config()
    {
        $lng    = $this->getServiceLocator()->get('MvcTranslator');     // Переводчик
        $auth   = $this->getServiceLocator()->get('authentification.Service'); // Сервис авторизации
        $user   = $this->getServiceLocator()->get('user.Model');
        
        // получаю авторизованного пользователя
        if($auth->hasIdentity() == true) $userFetch = $user->getUserByLogin($auth->getIdentity());      

        $pages = array(
            'navigation' => array(
                'default' => array(
                    array(
                        'label' => 'Главная',
                        'route' => 'social',
                        'pages' => array(
                            array(
                                'label' => 'Профиль',
                                'route' => 'profile'
                            )
                        )
                    ),
                    array(
                        'label' => 'Page #2',
                        'route' => 'page-2',
                    )
                )
            )
        );
        return $pages;
    }
}