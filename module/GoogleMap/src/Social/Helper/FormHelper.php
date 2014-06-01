<?php
namespace Social\Helper;

use Zend\View\Helper\AbstractHelper;
// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Social\Form; // Констуктор форм

/**
 * Помошник вида FormHelper выводит формы в шаблон
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/View/Helper/FormHelper.php
 */
class FormHelper extends AbstractHelper implements ServiceLocatorAwareInterface {

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

    public function __invoke($string, $additional = null)
    {
        switch($string)
        {
            case 'MenuSearchForm';
                return $this->getView()->partial('forms/menusearch', array('form' => new Form\MenuSearchForm(), 'add' => $additional));  // устанавливаю шаблон виджета
            break;
            case 'IndexSearchForm';
                return $this->getView()->partial('forms/indexsearch', array('form' => new Form\SimpleSearchForm(), 'add' => $additional));  // устанавливаю шаблон виджета
            break;
            case 'PersonalForm';
                return $this->getView()->partial('forms/personalform', array('form' => new Form\PersonalForm(), 'add' => $additional));  // устанавливаю шаблон виджета
            break;        }
    }
}