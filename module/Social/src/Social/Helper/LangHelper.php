<?php
namespace Social\Helper;

use Zend\View\Helper\AbstractHelper;
// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


/**
 * Помошник вида LangHelper выводит постянные переменные в шаблон. Текущая локаль
 * например так $this->getLocale('code')
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/View/Helper/LangHelper.php
 */
class LangHelper extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * $__authService Свойство хранения сервиса менеджера
     * @access protected
     * @var type object
     */
    protected $_serviceLocator;

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
     * Вывод в шаблоны через фабрику
     * @access private
     * @return string
     */
    public function __invoke($param)
    {
        switch($param)
        {
            case 'code': // получаю код текущего языка
                return $this->getCodeLang();
            break;
        }
    }
    
    /**
     * getCodeLang() возвращает текущий языковый код
     * @access private
     * @return string
     */    
    public function getCodeLang()
    {
        $locale = $this->_serviceLocator->getServiceLocator()->get('MvcTranslator');
        return substr($locale->getLocale(), 0,2);         
    }
}