<?php
namespace Social\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Version\Version as SysInfo; // системная информация
// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Debug\Debug as Debug;

/**
 * Помошник вида infoHelper выводит постянные переменные в шаблон. Системная информация
 * например так $this->getInfo()['version'] PHP 5.4
 * или $this->getInfo()->version если invoke преобразует (object)
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/View/Helper/InfoHelper.php
 */
class InfoHelper extends AbstractHelper implements ServiceLocatorAwareInterface {

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

    public function __invoke($plugin)
    {
        switch($plugin)
        {
            case 'config':
                return (object)$this->_sm->getServiceLocator()->get('Configuration');
            break;

            default:
                return '';
            break;
        }
    }
}