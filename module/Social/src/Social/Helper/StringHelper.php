<?php
namespace Social\Helper;

use Zend\View\Helper\AbstractHelper;
use SW\String\Format; // класс форматирования строк
// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Помошник вида StringHelper выводит постянные переменные в шаблон. Форматирую им строки
 * например так $this->getInfo()['version'] PHP 5.4
 * или $this->getInfo()->version если invoke преобразует (object)
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/View/Helper/StringHelper.php
 */
class StringHelper extends AbstractHelper implements ServiceLocatorAwareInterface {

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

    public function __invoke($type,$string)
    {
        $translator = $this->_sm->getServiceLocator()->get('MvcTranslator');
        switch($type)
        {
            case 'timediff':
                return Format::getTimeString($string, array(
                    '0' =>  array($translator->translate('day'), $translator->translate('days'), $translator->translate('days')),
                    '1' =>  array($translator->translate('hour'), $translator->translate('hours'), $translator->translate('hours')),
                    '2' =>  array($translator->translate('min.'), $translator->translate('min.'), $translator->translate('min.')),
                ));                
            break;
            case 'secondsformat':
                return Format::getRealtimeFromSeconds($string, array(
                    '0' =>  array($translator->translate('month'), $translator->translate('months'), $translator->translate('months')),
                    '1' =>  array($translator->translate('day'),  $translator->translate('days'), $translator->translate('days')),
                    '2' =>  array($translator->translate('hour'), $translator->translate('hours'), $translator->translate('hours')),
                    '3' =>  array($translator->translate('min.'), $translator->translate('min.'), $translator->translate('min.')),
                ));                
            break;        
        }

    }
}