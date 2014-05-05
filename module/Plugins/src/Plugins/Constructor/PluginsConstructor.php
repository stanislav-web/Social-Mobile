<?php
namespace Plugins\Constructor;

// интерфейсы вида
use Zend\View\Helper\AbstractHelper;
// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Фабрика виджетов
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Plugins/src/Plugins/Contructor/PluginsConstructor.php
 */
class PluginsConstructor extends AbstractHelper implements ServiceLocatorAwareInterface
{
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
    
    /**
     * zfService() Менеджер зарегистрированных сервисов ZF2
     * @access public
     * @return ServiceManager
     */
    public function zfService()
    {
        return $this->_sm->getServiceLocator();
    } 
    /**
     * Фабрика виджетов.
     * @param type $plugin тип виджета из набора
     * @return type
     * @throws \Exception
     */
    public function __invoke($plugin, $param = null)
    {
        switch($plugin)
        {
            case 'language':
                $config = $this->zfService()->get('Config'); // подключаю настройки
                return $this->getView()->partial('plugins/language', array('lang' => $config['languages']));  // устанавливаю шаблон виджета
            break;

            case 'flashwall':
                $params = $this->zfService()->get($plugin.'.Model')->get();
                if(!empty($params)) return $this->getView()->partial('plugins/zf_flash_wall/'.$plugin, array('params' => $params));  // устанавливаю шаблон виджета
                else return '';
            break;
            
            case 'bookmarks':
                $params = $this->zfService()->get($plugin.'.Model')->get();
                if(!empty($params)) return $this->getView()->partial('plugins/zf_plugin_bookmarks/'.$plugin, array('params' => $params));  // устанавливаю шаблон виджета
                else return '';
            break;
            
            case 'qrcode':
                $qr = $this->zfService()->get($plugin.'.Model')->get($param); // инициализирую при старте
                try {
                    if(!empty($qr)) return $this->getView()->partial('plugins/'.$plugin, array('qr' => $qr));  // устанавливаю шаблон
                    else return '';                    
                } 
                catch(\Exception $e) 
                {
                    return $e->getMessage();
                }
            break;
            
            case 'header':
                $header = $this->zfService()->get($plugin.'.Model')->get(); // инициализирую при старте
                if($header) return $this->getView()->partial('plugins/'.$plugin, array('param' => $param));  // устанавливаю шаблон виджета
            break;	    
	    
            case 'breadcrumbs':
                $nav = $this->zfService()->get($plugin.'.Model')->get(); // инициализирую при старте
                
                if(!empty($nav)) return $this->getView()->partial('plugins/'.$plugin, array());  // устанавливаю шаблон виджета
            break;
            
            case 'notices':
                $params = $this->zfService()->get($plugin.'.Model')->get($param);
                if(!empty($params)) return $this->getView()->partial('plugins/zf_plugin_notices/'.$param, array('params' => $params));  // устанавливаю шаблон виджета
                else return '';
            break;
            
            case 'menu': // Плагин выборки менюшек
                
                // тут очень внимательно!
                // нужно достать элементы меню, по коду меню `zf_menu_items` -> `menu`
                // и определить в какой из шаблонов меню их передовать. Это $param
                
                $items = $this->zfService()->get('menuItems.Service')->getMenuItems($param);
                $params = $this->zfService()->get($plugin.'.Model')->get($param);
                if($params != false) return $this->getView()->partial('plugins/zf_plugin_menu/'.$param, array('items' => $items));  // устанавливаю шаблон виджета
                else return '';
            break;
            
            case 'filesystem':
                return $this->getServiceLocator()->get($plugin.'.Model')->get();
            break;

            case 'mailtemplates': // Почтовые шаблоны
                return $this->getServiceLocator()->get($plugin.'.Model'); // ->get('tpl name'); after
            break;
        
            case 'events': // Шаблоны событий
                return $this->getServiceLocator()->get($plugin.'.Model'); // ->get('tpl name'); after
            break;
        
            case 'statistics':
                return $this->zfService()->get($plugin.'.Model')->set();
            break;
        
            case 'user':
                $params = $this->zfService()->get($plugin.'.Model')->get();

                if(!empty($params)) return $this->getView()->partial('plugins/zf_users/'.$plugin, array('params' => $params));  // устанавливаю шаблон виджета
            break;
        
            default:
                return '';
            break;
        }
    }
}